<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DR-2.1 — Create the audit_logs table.
     *
     * Every mutation that touches a booking or a tool status is recorded here
     * with the acting user, their IP address, and a UTC timestamp so that a
     * full history trail is always available.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // DR-2.1 — the authenticated user who triggered the action.
            // Nullable so that system/CLI actions (no HTTP request) can still
            // be recorded with a null user.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // DR-2.1 — IP address of the request that caused the action.
            $table->string('ip_address', 45)->nullable(); // 45 chars covers IPv6

            // DR-2.1 — exact UTC moment the action was performed.
            $table->timestamp('timestamp')->useCurrent();

            // Human-readable event name, e.g. "booking.confirmed", "tool.archived"
            $table->string('action');

            // Polymorphic subject — the model type and its primary key.
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');

            $table->index(['subject_type', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
