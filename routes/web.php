<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Conversas
    Route::resource('conversations', ConversationController::class);
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('conversations/{conversation}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::post('conversations/{conversation}/summarize', [ConversationController::class, 'summarize'])->name('conversations.summarize');
    Route::post('conversations/{conversation}/extract-tasks', [ConversationController::class, 'extractTasks'])->name('conversations.extractTasks');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Organizações
Route::get('/organization/create', [App\Http\Controllers\OrganizationController::class, 'create'])->name('organization.create');
Route::post('/organization', [App\Http\Controllers\OrganizationController::class, 'store'])->name('organization.store');
Route::get('/organization', [App\Http\Controllers\OrganizationController::class, 'show'])->name('organization.show');

// Convites
Route::get('/invitations/create', [App\Http\Controllers\InvitationController::class, 'create'])->name('invitations.create');
Route::post('/invitations', [App\Http\Controllers\InvitationController::class, 'store'])->name('invitations.store');
Route::get('/invitations/{token}/accept', [App\Http\Controllers\InvitationController::class, 'accept'])->name('invitations.accept');

require __DIR__.'/auth.php';
