<?php

use App\Http\Controllers\Api\V1\Admin\Category\CategoryController as AdminCategoryController;

use App\Http\Controllers\Api\V1\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

// Rutas públicas para frontend Vue
Route::prefix('v1')->group(function () {
    // Route::get('categories/tree', [CategoryController::class, 'tree']);
    // Route::get('categories/{slug}', [CategoryController::class, 'show']);
});

// Rutas admin protegidas
Route::prefix('v1/admin')->group(function () {
    Route::apiResource('categories', AdminCategoryController::class);
});
