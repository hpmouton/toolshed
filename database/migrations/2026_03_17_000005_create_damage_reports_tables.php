<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FR-13 — Damage reporting: when returning a tool, the renter declares the
 * condition. Staff can review, accept/reject/escalate damage reports.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();    // the renter
            $table->string('condition_declared');       // undamaged, minor_damage, major_damage
            $table->text('description')->nullable();    // required when not undamaged
            $table->string('status')->default('pending'); // pending, accepted, rejected, escalated
            $table->timestamps();
        });

        // FR-13.3 — Accepted damage reports generate a charge record
        Schema::create('damage_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency_code', 3);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_charges');
        Schema::dropIfExists('damage_reports');
    }
};
