<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
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

Route::get('/' , [HomeController::class , 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/post' , [PostController::class , 'store'] )->name('post.store');
    Route::put('/post/{post}' , [PostController::class , 'update'])->name('post.update');
    Route::delete('/post/{post}' , [PostController::class , 'destroy'])->name('post.delete');
    Route::post('/post/{post}/reaction' , [PostController::class , 'postReaction'])->name('post.reaction');
    Route::post('/post/{post}/comment' , [PostController::class , 'postComment'])->name('post.comment');
    Route::delete('/post/{comment}/comment' , [PostController::class , 'deleteComment'])->name('delete.comment');

});

Route::get('u/{user:username}' , [ProfileController::class , 'index'])->name('profile');
Route::post('/updateImage' , [ProfileController::class , 'updateImage'])->name('update.image');


require __DIR__.'/auth.php';
