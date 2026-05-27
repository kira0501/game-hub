<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\AdminGenreController;
use App\Http\Controllers\Admin\AdminPriceController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminStoreController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PcConfigController;
use App\Http\Controllers\PriceComparisonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', MainController::class)->name('home');
Route::get('/favicon.svg', fn () => response()->file(public_path('favicon.svg')))->name('favicon');
Route::get('/avatars/{filename}', [ProfileController::class, 'avatar'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('profile.avatar');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/search/suggest', [GameController::class, 'suggest'])->name('search.suggest');
Route::get('/games/{slug}', [GameController::class, 'show'])->name('games.show');
Route::get('/genres/{slug}', [GameController::class, 'genre'])->name('genres.show');
Route::get('/compare-prices', PriceComparisonController::class)->name('prices.compare');
Route::get('/recommendations', RecommendationController::class)->name('recommendations');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('/favorites/selected', [FavoriteController::class, 'destroySelected'])->name('favorites.destroy-selected');
    Route::post('/games/{game}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/games/{game}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/games/{game}/pc-check/{pcConfig}', [PcConfigController::class, 'quickCheck'])->name('games.pc-check');

    Route::get('/pc-check', [PcConfigController::class, 'index'])->name('pc.index');
    Route::post('/pc-check/configs', [PcConfigController::class, 'store'])->name('pc.store');
    Route::patch('/pc-check/configs/{pcConfig}', [PcConfigController::class, 'update'])->name('pc.update');
    Route::delete('/pc-check/configs/{pcConfig}', [PcConfigController::class, 'destroy'])->name('pc.destroy');
    Route::post('/pc-check/compare', [PcConfigController::class, 'compare'])->name('pc.compare');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::patch('games/{game}/toggle-active', [AdminGameController::class, 'toggleActive'])->name('games.toggle-active');
    Route::resource('games', AdminGameController::class)->except('show');
    Route::resource('genres', AdminGenreController::class)->except('show');
    Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', AdminUserController::class)->except('show');
    Route::resource('stores', AdminStoreController::class)->except('show');
    Route::get('prices/games/{game}/edit', [AdminPriceController::class, 'editGame'])->name('prices.games.edit');
    Route::put('prices/games/{game}', [AdminPriceController::class, 'updateGame'])->name('prices.games.update');
    Route::resource('prices', AdminPriceController::class)->except('show');
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
