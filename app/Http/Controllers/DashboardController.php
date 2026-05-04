<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $totalConversations = auth()->user()->conversations()->count();
        $totalMessages = Message::whereHas('conversation', function ($q) {
            $q->where('user_id', auth()->id());
        })->count();
        $totalTasks = Task::whereHas('conversation', function ($q) {
            $q->where('user_id', auth()->id());
        })->count();
        $pendingTasks = Task::whereHas('conversation', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('status', 'pending')->count();
        $doneTasks = Task::whereHas('conversation', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('status', 'done')->count();
        $highPriorityTasks = Task::whereHas('conversation', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('priority', 'high')->where('status', 'pending')->count();
        $recentConversations = auth()->user()->conversations()->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalConversations',
            'totalMessages',
            'totalTasks',
            'pendingTasks',
            'doneTasks',
            'highPriorityTasks',
            'recentConversations'
        ));
    }
}
