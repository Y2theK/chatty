<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

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


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/conversations', 301)->name('dashboard');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations/create', [ConversationController::class, 'createConversation'])->name('conversations.createConversation'); // create new conversation
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/add', [ConversationController::class, 'addGroup'])->name('conversations.addGroup'); // invite to group or conversation
    Route::delete('/conversations/{conversation}/leave', [ConversationController::class, 'leaveConversation'])->name('conversations.leaveConversation'); //leave conversation

    Route::post('/conversations/{conversation}/messages', [ChatMessageController::class, 'store'])->name('messages.store'); // message store
    Route::delete('/conversations/{conversation}/messages/{message}', [ChatMessageController::class, 'destroy'])->name('messages.destroy'); // message delete

    Route::post('/users/updateLastActiveAt', [UserController::class, 'updateLastActiveAt'])->name('conversations.updateLastActiveAt'); // update updateLastActiveAt

    Route::get('users',[UserController::class,'index'])->name('users.index');

});

require __DIR__.'/auth.php';
