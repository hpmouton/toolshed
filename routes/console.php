<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// FR-10.2 — Reminder notifications for bookings ending within 48 hours
Schedule::command('bookings:send-reminders')->dailyAt('08:00');

// FR-10.3 — Overdue booking notifications
Schedule::command('bookings:notify-overdue')->dailyAt('09:00');
