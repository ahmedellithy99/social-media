<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/' , [HomeController::class , 'index'])->name('home');

Route::middleware('auth')->group(function () {
    //Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Post 
    Route::get('/post/{post}' , [PostController::class , 'show'] )->name('post.show');
    Route::post('/post' , [PostController::class , 'store'] )->name('post.store');
    Route::put('/post/{post}' , [PostController::class , 'update'])->name('post.update');
    Route::delete('/post/{post}' , [PostController::class , 'destroy'])->name('post.delete');
    Route::post('/post/{post}/reaction' , [PostController::class , 'postReaction'])->name('post.reaction');
    Route::post('/post/{post}/comment' , [PostController::class , 'postComment'])->name('post.comment');
    Route::delete('/post/{comment}/comment' , [PostController::class , 'deleteComment'])->name('delete.comment');
    Route::post('/post/{comment}/reactions' , [PostController::class , 'commentReaction'])->name('comment.reaction');

    // User

    Route::post('user/follow/{user}' , [UserController::class , 'follow'])->name('user.follow');
    Route::post('user/unfollow/{user}' , [UserController::class , 'unfollow'])->name('user.unfollow');
    
    // Search From Nav Bar
    Route::get('/users', [SearchController::class, 'userSearch'])
        ->name('search.users');

    // Chat 
    Route::get('/chat/{chat:subId}' ,[ChatController::class , 'index'] )->name('chat.index');
    Route::post('/chat/sendMessage' ,[ChatController::class , 'store'] )->name('chat.store');

});

Route::get('u/{user:username}' , [ProfileController::class , 'index'])->name('profile');
Route::post('/updateImage' , [ProfileController::class , 'updateImage'])->name('update.image');


require __DIR__.'/auth.php';
