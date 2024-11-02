<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chats', [ChatController::class,'index'])->middleware(['auth', 'verified'])->name('chat');
Route::post('/chats/public', [ChatController::class,'store'])->middleware(['auth', 'verified'])->name('chat.public');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/conversations', 301)->name('dashboard');
    Route::get('/chat/{user}', [MessageController::class, 'show'])->name('chat.user');

    Route::get('/messages/{user}', [MessageController::class, 'getMessages']);
    Route::post('/messages/{user}', [MessageController::class, 'sendMessage']);

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');

    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}', [ConversationController::class, 'store'])->name('conversations.store');

    Route::post('/conversations/{conversation}/add', [ConversationController::class, 'addGroup'])->name('conversations.addGroup');


});

require __DIR__.'/auth.php';
