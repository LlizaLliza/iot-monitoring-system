<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Machine\Index as MachinesIndex;
use App\Livewire\Machine\Form as MachinesForm;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/machines', MachinesIndex::class)->name('machines.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/machines/create', MachinesForm::class)->name('machines.create');
        Route::get('/machines/{machine}/edit', MachinesForm::class)->name('machines.edit');
    });
});

require __DIR__.'/auth.php';
