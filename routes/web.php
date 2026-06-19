<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\CabangController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cabang', [CabangController::class, 'index'])->name('cabang')->middleware('can:admin');
    
    Route::get('/piket/input', [PiketController::class, 'create'])->name('piket.input');
    Route::post('/piket/input', [PiketController::class, 'store'])->name('piket.store');
    
    Route::get('/piket/edit/{id}', [PiketController::class, 'edit'])->name('piket.edit');
    Route::put('/piket/update/{id}', [PiketController::class, 'update'])->name('piket.update');
    
    Route::get('/piket/history', [PiketController::class, 'history'])->name('piket.history');
    Route::get('/piket/laporan', [PiketController::class, 'laporan'])->name('piket.laporan');
    
    Route::get('/piket/template', [PiketController::class, 'downloadTemplate'])->name('piket.template');
    Route::get('/piket/export-pdf/{id}', [PiketController::class, 'exportPdf'])->name('piket.exportPdf');
    Route::get('/piket/download-lampiran/{id}', [PiketController::class, 'downloadLampiran'])->name('piket.downloadLampiran');
    // Admin Routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\AdminUserController::class)->except(['show']);
        
        Route::post('/templates/add-item', [\App\Http\Controllers\TemplateController::class, 'addItem'])->name('templates.add');
        Route::post('/templates/delete-item', [\App\Http\Controllers\TemplateController::class, 'deleteItem'])->name('templates.delete');
    });
});
