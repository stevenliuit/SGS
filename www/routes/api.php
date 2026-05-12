<?php

use App\Http\Controllers\DBI\IndexController as DBIIndexController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\Tinfoil\IndexController as TinfoilIndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('tinfoil', TinfoilIndexController::class)
    ->name('tinfoil');

Route::get('dbi/{path?}', DBIIndexController::class)
    ->where('path', '.*')
    ->name('dbi');

/*
|--------------------------------------------------------------------------
| File Manager Routes
|--------------------------------------------------------------------------
*/
Route::get('file-manager', [FileManagerController::class, 'index'])->name('file-manager');
Route::get('file-manager/api/list', [FileManagerController::class, 'list'])->name('file-manager.list');
Route::post('file-manager/api/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
Route::get('file-manager/api/download', [FileManagerController::class, 'download'])->name('file-manager.download');
Route::get('file-manager/api/download-folder', [FileManagerController::class, 'downloadFolder'])->name('file-manager.download-folder');
Route::post('file-manager/api/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
Route::post('file-manager/api/rename', [FileManagerController::class, 'rename'])->name('file-manager.rename');
Route::post('file-manager/api/create-folder', [FileManagerController::class, 'createFolder'])->name('file-manager.create-folder');
