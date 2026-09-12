<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\Payments\LemonSqueezyController;
use App\Http\Controllers\Payments\PaddleController;
use App\Http\Controllers\Payments\StripeController;
use App\Http\Controllers\AtaController;
use App\Http\Controllers\ExcelSheetController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\Subscribed;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('sitemap', [SitemapController::class, 'index'])->name('sitemap');

// Demo Coming Soon Page
Route::get('coming-soon', function () {
    return view('pages.coming-soon');
})->name('coming-soon');

Route::prefix('auth')->group(function () {
    Route::get('/redirect/{driver}', [SocialiteController::class, 'redirect'])
        ->name('socialite.redirect');
    Route::get('/callback/{driver}', [SocialiteController::class, 'callback'])
        ->name('socialite.callback');

    // Magic Links
    Route::post('/magic-link', [MagicLinkController::class, 'sendMagicLink'])->name('magic.link');
    Route::get('/magic-link/{token}', [MagicLinkController::class, 'loginWithMagicLink'])->name('magic.link.login');
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{article:slug}', [BlogController::class, 'article'])->name('blog.article');

// Dynamic Open Graph Image
Route::get('og-image/{title?}/{description?}', OgImageController::class)->name('og-image');

// For testing and modifying the default image template
Route::get('og-image-testing', function () {
    return view('seo.image', [
        'title' => 'Your dynamic og image',
        'description' => 'Your dynamic og image description', // optional
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/allcalendar', [DashboardController::class, 'allcalendar'])->name('allcalendar');
    Route::get('/noitesdecampo', [ExcelSheetController::class, 'noitesCampo'])->name('noites-campo');
    Route::get('/horasmar', [ExcelSheetController::class, 'horasMar'])->name('horasmar');

    // Atas do Agrupamento
    Route::post('/atas', [AtaController::class, 'store'])->name('atas.store');

    // Eventos para o calendário (FullCalendar consome isto via fetch)
    Route::get('/api/events', [AtaController::class, 'events'])->name('api.events');
});
