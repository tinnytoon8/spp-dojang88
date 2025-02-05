<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\Payment;

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin/payment/{id}', Payment::class)->name('filament.pages.payment');
