<?php

use App\Http\Controllers\DishController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DishController::class, 'index'])->name('dishes.index');
Route::view('/about', 'about')->name('about');

Route::get('/areas/{areaSlug}', [DishController::class, 'area'])
    ->whereAlpha('areaSlug')
    ->name('dishes.area');

Route::get('/ingredients/{name}', [DishController::class, 'ingredient'])->name('dishes.ingredient');

Route::get('/dishes/{dish}', [DishController::class, 'show'])->name('dishes.show');

Route::get('/sitemap.xml', [DishController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', function () {
    $body = "User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml')."\n";

    return response($body, 200, ['Content-Type' => 'text/plain']);
});
