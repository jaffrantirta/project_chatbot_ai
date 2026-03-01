<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

// Public chat — identified by session_token (UUID), not by DB id
Route::prefix('chat')->group(function () {
    Route::get('/{token}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/{token}/send', [ChatController::class, 'send'])->name('chat.send');
});
