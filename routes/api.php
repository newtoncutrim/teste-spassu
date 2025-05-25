<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TopicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('authors', AuthorController::class)->names('authors');
Route::get('authorswithbooks', [AuthorController::class, 'findWithBooksAndTopics'])->name('authorswithbooks');
Route::resource('topics', TopicController::class)->names('topics');
Route::resource('books', BookController::class)->names('books');

Route::get('totalbooks', [BookController::class, 'count'])->name('totalbooks');
Route::get('totalauthors', [AuthorController::class, 'count'])->name('totalauthors');
Route::get('totaltopics', [TopicController::class, 'count'])->name('totaltopics');