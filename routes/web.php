<?php

use App\Http\Controllers\DishController;
use App\Http\Controllers\MealPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [MealPlanController::class, 'index'])->name('dashboard');
    Route::post('/meal-plan', [MealPlanController::class, 'save'])->name('meal-plan.save');
    Route::resource('dishes', DishController::class)->except(['show']);
});

require __DIR__.'/auth.php';
