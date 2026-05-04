<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $conversation->messages()->create($request->only('sender_name', 'content'));

        return redirect()->route('conversations.show', $conversation);
    }
}
