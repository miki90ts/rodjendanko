<?php

use App\Http\Controllers\BirthdayContactController;
use App\Http\Controllers\BirthdayDeliveryController;
use App\Http\Controllers\BirthdayInvitationController;
use App\Http\Controllers\BirthdayMessageController;
use App\Http\Controllers\BirthdayPreviewController;
use App\Http\Controllers\BirthdaySettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicBirthdayInvitationController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('pozivnica/{publicToken}', [PublicBirthdayInvitationController::class, 'show'])->name('invitations.public');
Route::post('pozivnica/{publicToken}/odgovor/{guestToken}', [PublicBirthdayInvitationController::class, 'respond'])->name('invitations.rsvp');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('birthdays', BirthdayContactController::class)->parameters(['birthdays' => 'birthdayContact']);
    Route::post('invitations/{invitation}/send', [BirthdayInvitationController::class, 'send'])->name('invitations.send');
    Route::resource('invitations', BirthdayInvitationController::class);
    Route::resource('messages', BirthdayMessageController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['messages' => 'birthdayMessage']);
    Route::get('deliveries', [BirthdayDeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('deliveries/{birthdayDelivery}/retry', [BirthdayDeliveryController::class, 'retry'])->name('deliveries.retry');
    Route::post('previews/message', [BirthdayPreviewController::class, 'message'])->name('previews.message');
    Route::post('previews/email', [BirthdayPreviewController::class, 'email'])->name('previews.email');
    Route::get('settings/birthdays', [BirthdaySettingsController::class, 'edit'])->name('birthday-settings.edit');
    Route::put('settings/birthdays', [BirthdaySettingsController::class, 'update'])->name('birthday-settings.update');
});

require __DIR__.'/settings.php';
