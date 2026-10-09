<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('workspaces', [WorkspaceController::class, 'index']);
        Route::post('workspaces', [WorkspaceController::class, 'store']);
        Route::get('workspaces/{workspace}', [WorkspaceController::class, 'show']);

        Route::get('workspaces/{workspace}/documents', [DocumentController::class, 'index']);
        Route::post('workspaces/{workspace}/documents', [DocumentController::class, 'store']);
        Route::delete('workspaces/{workspace}/documents/{document}', [DocumentController::class, 'destroy']);

        Route::get('workspaces/{workspace}/conversations', [ConversationController::class, 'index']);
        Route::post('workspaces/{workspace}/conversations', [ConversationController::class, 'store']);
        Route::get('workspaces/{workspace}/conversations/{conversation}', [ConversationController::class, 'show']);
        Route::post('workspaces/{workspace}/conversations/{conversation}/messages', [ConversationController::class, 'message']);
    });
});
