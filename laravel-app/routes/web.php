<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\PortfolioItemController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Site\ClientController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| سایت عمومی (Public site) — نگاه مدیا
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('/clients/{client:slug}', [ClientController::class, 'show'])->name('clients.show');
Route::get('/clients/{client:slug}/{category:slug}', [ClientController::class, 'category'])->name('clients.category');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| پنل مدیریت نگاه مدیا — /dashbord/app
|--------------------------------------------------------------------------
*/
Route::prefix('dashbord/app')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest:admin');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit')->middleware('guest:admin');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/new', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::post('/clients/reorder', [AdminClientController::class, 'reorder'])->name('clients.reorder');
        Route::get('/clients/{client}', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::post('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::post('/clients/{client}/delete', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/clients/{client}/toggle-published', [AdminClientController::class, 'togglePublished'])->name('clients.toggle-published');
        Route::post('/clients/{client}/toggle-featured', [AdminClientController::class, 'toggleFeatured'])->name('clients.toggle-featured');

        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::post('/portfolio-items', [PortfolioItemController::class, 'store'])->name('portfolio-items.store');
        Route::post('/portfolio-items/{portfolioItem}/toggle-featured', [PortfolioItemController::class, 'toggleFeatured'])->name('portfolio-items.toggle-featured');
        Route::post('/portfolio-items/{portfolioItem}/delete', [PortfolioItemController::class, 'destroy'])->name('portfolio-items.destroy');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::post('/fonts', [FontController::class, 'store'])->name('fonts.store');
        Route::post('/fonts/{font}/delete', [FontController::class, 'destroy'])->name('fonts.destroy');

        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('messages.toggle-read');
        Route::post('/messages/{message}/delete', [MessageController::class, 'destroy'])->name('messages.destroy');

        Route::post('/change-password', [PasswordController::class, 'update'])->name('password.update');
    });
});
