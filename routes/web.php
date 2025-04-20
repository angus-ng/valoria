<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MonsterController;
use App\Http\Controllers\HabitatController;
use App\Http\Controllers\ArmorSetController;
use App\Http\Controllers\CrownController;
use App\Enums\UserRole;
use App\Http\Middleware\IsAdmin;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role === UserRole::Admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])
    ->prefix('api')
    ->group(function (){
        Route::get('monsters/all', [MonsterController::class, 'all']);
        Route::get('habitats', [HabitatController::class, 'index']);
        Route::get('armor-sets', [ArmorSetController::class, 'index']);
    });

Route::middleware(['auth', IsAdmin::class])
    ->prefix('api/admin')
    ->group(function () {
        Route::resource('habitats', HabitatController::class)->only(['store', 'destroy']);
        Route::resource('monsters', MonsterController::class)->only(['store', 'destroy']);
        Route::resource('armor-sets', ArmorSetController::class)->only(['store', 'destroy']);
    });

    
Route::middleware(['auth'])
    ->group(function (){
        Route::get('/monsters', [MonsterController::class, 'index'])->name('monsters.index');
        Route::get('/monsters/{monster:slug}', [MonsterController::class, 'show'])
            ->name('monsters.show');
        Route::get('/crowns', [CrownController::class, 'index'])->name('crowns.index');
        Route::post('/crowns/toggle', [CrownController::class, 'toggle'])->name('crowns.toggle');
    });

Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('admin.dashboard');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
