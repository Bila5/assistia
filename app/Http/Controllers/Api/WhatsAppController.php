<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function webhook(Request $request)
    {
        $from = $request->input('From'); // número do remetente
        $body = $request->input('Body'); // conteúdo da mensagem
        $profileName = $request->input('ProfileName', 'WhatsApp User');

        if (!$from || !$body) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        // Encontrar ou criar conversa para este número
        $conversation = Conversation::firstOrCreate(
            ['whatsapp_from' => $from],
            [
                'title' => 'WhatsApp - ' . $profileName,
                'type' => 'chat',
                'user_id' => 1, // utilizador admin por defeito
            ]
        );

        // Guardar a mensagem
        $conversation->messages()->create([
            'sender_name' => $profileName,
            'content' => $body,
        ]);

        return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 200)
            ->header('Content-Type', 'text/xml');
    }
}
