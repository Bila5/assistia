<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organization;

        if ($organization) {
            $conversations = Conversation::where('organization_id', $organization->id)
                ->latest()->get();
        } else {
            $conversations = $request->user()->conversations()->latest()->get();
        }

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:meeting,chat',
        ]);

        $organization = $request->user()->organization;

        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'organization_id' => $organization ? $organization->id : null,
            'title' => $request->title,
            'type' => $request->type,
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Conversation $conversation)
    {
        return response()->json($conversation->load('messages', 'tasks'));
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return response()->json(['message' => 'Conversa apagada com sucesso']);
    }

    public function summarize(Conversation $conversation)
    {
        $messages = $conversation->messages()->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Não há mensagens para resumir.'], 400);
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
            return response()->json(['summary' => $summary]);
        }

        return response()->json(['message' => 'Não foi possível gerar o resumo.'], 500);
    }

    public function extractTasks(Conversation $conversation)
    {
        $messages = $conversation->messages()->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Não há mensagens para analisar.'], 400);
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
            return response()->json(['message' => count($tasks) . ' tarefa(s) criada(s) automaticamente!']);
        }

        return response()->json(['message' => 'Não foi possível extrair tarefas.'], 500);
    }
}
