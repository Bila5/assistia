<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Conversation $conversation)
    {
        $tasks = $conversation->tasks()->latest()->get();
        return response()->json($tasks);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $task = $conversation->tasks()->create($request->only('title', 'assigned_to', 'priority'));

        return response()->json($task, 201);
    }

    public function toggle(Task $task)
    {
        $task->update([
            'status' => $task->status === 'pending' ? 'done' : 'pending'
        ]);

        return response()->json($task);
    }
}
