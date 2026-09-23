<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploradorgestaoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LobitosGestaoController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\Payments\LemonSqueezyController;
use App\Http\Controllers\Payments\PaddleController;
use App\Http\Controllers\Payments\StripeController;
use App\Http\Controllers\AtaController;
use App\Http\Controllers\ExcelSheetController;
use App\Http\Controllers\PioneirosGestaoController;
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

    // 1ª Secção: Alcateia (Lobitos)
    Route::prefix('alcateia')->name('alcateia.')->middleware(['seccao:lobitos'])->group(function () {
        Route::get('/dashboard', [LobitosGestaoController::class, 'index'])->name('dashboard');
        Route::post('/exploradores/gestao/user', [LobitosGestaoController::class, 'storeUser'])->name('lobitos-gestao.store-user');
        Route::post('/exploradores/gestao/toggle', [LobitosGestaoController::class, 'toggleObjetivo'])->name('lobitos-gestao.toggle');
        Route::post('/exploradores/gestao/toggle-bulk', [LobitosGestaoController::class, 'toggleObjetivoBulk'])->name('lobitos-gestao.toggle-bulk');
    });

    // 2ª Secção: Expedição (Exploradores)
    Route::prefix('expedicao')->name('expedicao.')->middleware(['seccao:exploradores'])->group(function () {
        Route::get('/dashboard', [ExploradorgestaoController::class, 'index'])->name('dashboard');
        Route::post('/exploradores/gestao/user', [ExploradorgestaoController::class, 'storeUser'])->name('exploradores-gestao.store-user');
        Route::post('/exploradores/gestao/toggle', [ExploradorgestaoController::class, 'toggleObjetivo'])->name('exploradores-gestao.toggle');
        Route::post('/exploradores/gestao/toggle-bulk', [ExploradorGestaoController::class, 'toggleObjetivoBulk'])->name('exploradores-gestao.toggle-bulk');
    });

    // 3ª Secção: Comunidade (Pioneiros)
    Route::prefix('comunidade')->name('comunidade.')->middleware(['seccao:pioneiros'])->group(function () {
        Route::get('/dashboard', [PioneirosGestaoController::class, 'index'])->name('dashboard');
        Route::post('/pioneiros/gestao/user', [PioneirosgestaoController::class, 'storeUser'])->name('pioneiros-gestao.store-user');
        Route::post('/pioneiros/gestao/toggle', [PioneirosGestaoController::class, 'toggleObjetivo'])->name('pioneiros-gestao.toggle');
        Route::post('/pioneiros/gestao/toggle-bulk', [PioneirosGestaoController::class, 'toggleObjetivoBulk'])->name('pioneiros-gestao.toggle-bulk');
    });

    // 4ª Secção: Clã (Caminheiros)
    Route::prefix('cla')->name('cla.')->middleware(['seccao:cla'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'cla'])->name('dashboard');
        // Noites de Campo
        Route::get('/noitesdecampo', [ExcelSheetController::class, 'noitesCampo'])->name('noites-campo');
        Route::post('/noitesdecampo/atividade', [ExcelSheetController::class, 'storeAtividadeNoitesCampo'])->name('noites-campo.store');
        Route::post('/noitesdecampo/toggle', [ExcelSheetController::class, 'toggleParticipacaoNoitesCampo'])->name('noites-campo.toggle');
        Route::delete('noites-campo/delete', [ExcelSheetController::class, 'destroyAtividadeNoitesCampo'])->name('cla.noites-campo.destroy');
        // Horas de Mar
        Route::get('/horasmar', [ExcelSheetController::class, 'horasMar'])->name('horasmar');
        Route::post('/horasmar/atividade', [ExcelSheetController::class, 'storeAtividadeHorasMar'])->name('horasmar.store');
        Route::post('/horasmar/toggle', [ExcelSheetController::class, 'toggleParticipacaoHorasMar'])->name('horasmar.toggle');
    });

    // Redirecionamentos de retrocompatibilidade
    Route::redirect('/noitesdecampo', '/cla/noitesdecampo');
    Route::redirect('/horasmar', '/cla/horasmar');

    // Atas do Agrupamento
    Route::post('/atas', [AtaController::class, 'store'])->name('atas.store');

    // Eventos para o calendário (FullCalendar consome isto via fetch)
    Route::get('/api/events', [AtaController::class, 'events'])->name('api.events');

    Route::get('/limpar-cache-sheets', function () {
        \Illuminate\Support\Facades\Cache::forget('sheet.noites_campo');
        \Illuminate\Support\Facades\Cache::forget('sheet.horas_mar');
        return 'Cache das sheets limpa. Podes voltar às páginas normais.';
    })->name('limpar-cache-sheets');
});
