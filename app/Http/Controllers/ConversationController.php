<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = auth()->user()->conversations()->latest()->get();
        return view('conversations.index', compact('conversations'));
    }

    public function create()
    {
        return view('conversations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:meeting,chat',
        ]);

        auth()->user()->conversations()->create($request->only('title', 'type'));

        return redirect()->route('conversations.index');
    }

    public function show(Conversation $conversation)
    {
        $messages = $conversation->messages()->latest()->get();
        $tasks = $conversation->tasks()->latest()->get();
        return view('conversations.show', compact('conversation', 'messages', 'tasks'));
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return redirect()->route('conversations.index');
    }

    public function summarize(Conversation $conversation)
    {
        $messages = $conversation->messages()->get();

        if ($messages->isEmpty()) {
            return back()->with('error', 'Não há mensagens para resumir.');
        }

        $text = $messages->map(function ($m) {
            return $m->sender_name . ': ' . $m->content;
        })->join("\n");

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Analisa esta conversa e responde em português com o seguinte formato estruturado:

## 📌 Pontos-Chave
- Lista os pontos mais importantes discutidos

## ✅ Decisões Tomadas
- Lista as decisões concretas que foram tomadas

## ➡️ Próximos Passos
- Lista as acções a tomar a seguir

## 🔇 Informação Irrelevante
- Indica brevemente o que foi filtrado por ser ruído ou irrelevante

Conversa a analisar:
{$text}"]
                        ]
                    ]
                ]
            ]
        );

        $summary = $response->json('candidates.0.content.parts.0.text');

        if ($summary) {
            $conversation->update(['summary' => $summary]);
            $conversation->refresh();
            return redirect()->route('conversations.show', $conversation)->with('success', 'Resumo gerado com sucesso!');
        }

        return back()->with('error', 'Não foi possível gerar o resumo. Tenta novamente.');
    }

    public function extractTasks(Conversation $conversation)
    {
        $messages = $conversation->messages()->get();

        if ($messages->isEmpty()) {
            return back()->with('error', 'Não há mensagens para analisar.');
        }

        $text = $messages->map(function ($m) {
            return $m->sender_name . ': ' . $m->content;
        })->join("\n");

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Analisa esta conversa e extrai as tarefas mencionadas. Responde APENAS com JSON no formato: [{"title": "tarefa", "assigned_to": "nome ou null"}]. Sem texto adicional.\n\n' . $text]
                        ]
                    ]
                ]
            ]
        );

        $raw = $response->json('candidates.0.content.parts.0.text');
        $raw = preg_replace('/```json|```/', '', $raw);
        $tasks = json_decode(trim($raw), true);

        if ($tasks && is_array($tasks)) {
            foreach ($tasks as $task) {
                $conversation->tasks()->create([
                    'title' => $task['title'],
                    'assigned_to' => $task['assigned_to'] ?? null,
                    'status' => 'pending',
                ]);
            }
            return redirect()->route('conversations.show', $conversation)
                ->with('success', count($tasks) . ' tarefa(s) criada(s) automaticamente!');
        }

        return back()->with('error', 'Não foi possível extrair tarefas.');
    }
}
