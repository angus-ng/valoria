<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MonsterController;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // if ($user->is_admin) {
        //     return redirect()->route('monsters.index');
        // }

        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'isadmin'])->prefix('admin')->group(function () {
    Route::resource('monsters', MonsterController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
