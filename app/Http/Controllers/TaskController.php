<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $conversation->tasks()->create($request->only('title', 'assigned_to', 'priority'));

        return redirect()->route('conversations.show', $conversation);
    }

    public function toggle(Task $task)
    {
        $task->update([
            'status' => $task->status === 'pending' ? 'done' : 'pending'
        ]);

        return back();
    }
}

