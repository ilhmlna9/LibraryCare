<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\IntegrationWebhookController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Visitor\VisitorDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Landing, Form Pengaduan, Lacak Laporan)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/complaints', [LandingController::class, 'storeComplaint'])->name('complaints.store');
Route::get('/track', [LandingController::class, 'track'])->name('complaints.track');
Route::post('/track/{ticket}/satisfaction', [LandingController::class, 'submitSatisfaction'])->name('complaints.satisfaction');
Route::get('/google-forms', [LandingController::class, 'showGoogleForm'])->name('google-forms.simulate');

/*
|--------------------------------------------------------------------------
| Webhook Integrations (Google Forms, Telegram, WhatsApp)
|--------------------------------------------------------------------------
*/
Route::post('/integration/google-forms', [IntegrationWebhookController::class, 'googleForms'])->name('integration.google-forms.webhook');
Route::post('/integration/telegram/webhook', [IntegrationWebhookController::class, 'telegramWebhook'])->name('integration.telegram.webhook');
Route::match(['get', 'post'], '/integration/whatsapp/webhook', [IntegrationWebhookController::class, 'whatsappWebhook'])->name('integration.whatsapp.webhook');

/*
|--------------------------------------------------------------------------
| Fallback Dashboard Redirector
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('visitor.dashboard');
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Visitor / Mahasiswa Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('visitor')->name('visitor.')->group(function () {
    Route::get('/dashboard', [VisitorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [VisitorDashboardController::class, 'history'])->name('history');
    Route::get('/complaints/{ticket}', [VisitorDashboardController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{ticket}/reply', [VisitorDashboardController::class, 'reply'])->name('complaints.reply');
    Route::get('/profile', [VisitorDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [VisitorDashboardController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Perpustakaan Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Complaint Management
    Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{id}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.status');
    Route::post('/complaints/{id}/reply', [AdminComplaintController::class, 'storeReply'])->name('complaints.reply');
    Route::delete('/complaints/{id}', [AdminComplaintController::class, 'destroy'])->name('complaints.destroy');

    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('/categories/{id}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

    // Visitor Management (Dinonaktifkan - dialihkan ke daftar pengaduan)
    Route::get('/visitors', function () {
        return redirect()->route('admin.complaints.index');
    })->name('visitors.index');
    Route::get('/visitors/{id}', function () {
        return redirect()->route('admin.complaints.index');
    })->name('visitors.show');

    // Integrations & Notification Settings
    Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('/integrations', [IntegrationController::class, 'update'])->name('integrations.update');
    Route::post('/integrations/google-forms/regenerate-key', [IntegrationController::class, 'regenerateGoogleFormsApiKey'])->name('integrations.google-forms.regenerate');
    Route::post('/integrations/test/whatsapp', [IntegrationController::class, 'testWhatsApp'])->name('integrations.test.whatsapp');
    Route::post('/integrations/test/telegram', [IntegrationController::class, 'testTelegram'])->name('integrations.test.telegram');
    Route::post('/integrations/test/google-forms', [IntegrationController::class, 'testGoogleFormsSimulation'])->name('integrations.test.google-forms');
});

/*
|--------------------------------------------------------------------------
| Breeze Default Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
