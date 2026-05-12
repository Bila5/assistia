<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Conversas - todos podem ver
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');

    // Conversas - apenas admin e manager podem criar/apagar
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/conversations/create', [ConversationController::class, 'create'])->name('conversations.create');
        Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
        Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
        Route::post('conversations/{conversation}/summarize', [ConversationController::class, 'summarize'])->name('conversations.summarize');
        Route::post('conversations/{conversation}/extract-tasks', [ConversationController::class, 'extractTasks'])->name('conversations.extractTasks');
    });

    // Mensagens - todos podem enviar
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');

    // Tarefas - todos podem criar e toggle
    Route::post('conversations/{conversation}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Organizações
    Route::get('/organization/create', [OrganizationController::class, 'create'])->name('organization.create');
    Route::post('/organization', [OrganizationController::class, 'store'])->name('organization.store');
    Route::get('/organization', [OrganizationController::class, 'show'])->name('organization.show');

    // Convites - apenas admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    });

    // Aceitar convite - qualquer utilizador autenticado
    Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/auth.php';
