<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments;  
use App\Livewire\Examination;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('dashboard/payments', [Payments::class, 'index']);

Route::get('dashboard/exams/{examId?}', Examination::class)->name('dashboard.exams');
