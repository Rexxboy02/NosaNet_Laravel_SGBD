<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ModerationController;

// Página de inicio (Ver mensajes aprobados)
Route::get('/', [MessageController::class, 'index'])->name('home');

// Autenticación
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Mensajes y Moderación
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation');
Route::post('/moderation/action', [ModerationController::class, 'process'])->name('moderation.action');