<?php

use App\Livewire\AuditLogViewer;
use App\Livewire\BookingDatePicker;
use App\Livewire\BookingList;
use App\Livewire\DamageReportManager;
use App\Livewire\DepotFinder;
use App\Livewire\DepotManager;
use App\Livewire\StaffBookingManager;
use App\Livewire\ToolGallery;
use App\Livewire\ToolManager;
use App\Livewire\UserManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Tools — browse the gallery and book an individual tool
    Route::get('tools', ToolGallery::class)->name('tools.index');
    Route::get('tools/{tool}', BookingDatePicker::class)->name('tools.show');

    // Bookings — the current user's booking history
    Route::get('bookings', BookingList::class)->name('bookings.index');

    // Depots — find nearby depots + manage location/currency preferences
    Route::get('depots', DepotFinder::class)->name('depots.index');

    // ──────────────────────────────────────────────────────────────────────
    // FR-0.4 / FR-0.5 — Staff routes (staff + admin)
    // ──────────────────────────────────────────────────────────────────────
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('audit-log', AuditLogViewer::class)->name('audit-log');
        Route::get('bookings', StaffBookingManager::class)->name('bookings');
        Route::get('damage-reports', DamageReportManager::class)->name('damage-reports');
    });

    // ──────────────────────────────────────────────────────────────────────
    // FR-0.5 — Admin routes
    // ──────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('tools', ToolManager::class)->name('tools');
        Route::get('depots', DepotManager::class)->name('depots');
        Route::get('users', UserManager::class)->name('users');
    });
});

require __DIR__.'/settings.php';
