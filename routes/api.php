<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\WhatsAppController;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/invitations/{token}/accept', [OrganizationController::class, 'acceptInvite']);

// WhatsApp Webhook - público
Route::post('/whatsapp/webhook', [WhatsAppController::class, 'webhook']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Conversas
    Route::apiResource('conversations', ConversationController::class);
    Route::post('conversations/{conversation}/summarize', [ConversationController::class, 'summarize']);
    Route::post('conversations/{conversation}/extract-tasks', [ConversationController::class, 'extractTasks']);

    // Mensagens
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store']);
    Route::get('conversations/{conversation}/messages', [MessageController::class, 'index']);

    // Tarefas
    Route::post('conversations/{conversation}/tasks', [TaskController::class, 'store']);
    Route::get('conversations/{conversation}/tasks', [TaskController::class, 'index']);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);

    // Organização
    Route::get('/organization', [OrganizationController::class, 'show']);
    Route::post('/organization', [OrganizationController::class, 'store']);
    Route::post('/organization/invite', [OrganizationController::class, 'invite']);
});
