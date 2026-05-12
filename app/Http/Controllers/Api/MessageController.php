<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Conversation $conversation)
    {
        $messages = $conversation->messages()->latest()->get();
        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $message = $conversation->messages()->create($request->only('sender_name', 'content'));

        return response()->json($message, 201);
    }
}
