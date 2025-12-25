<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VocabController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vocab-test', [VocabController::class, 'index'])->name('vocab.test');
Route::post('/vocab-test/check', [VocabController::class, 'check'])->name('vocab.test.check');
Route::post('/vocab/check-answer', [VocabController::class, 'checkSingle'])->name('vocab.check.single');
Route::get('/vocab/article-match/{lessonId}', [VocabController::class, 'articleMatch'])
    ->name('vocab.article.match');

Route::post('/vocab/test/ajax', [VocabController::class, 'ajaxCheck'])
    ->name('vocab.test.ajax');
