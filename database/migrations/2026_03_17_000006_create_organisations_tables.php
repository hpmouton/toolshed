<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FR-16 — Organisations: users can belong to an organisation with a private
 * tool catalogue, monthly invoicing, and a credit limit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('credit_limit_cents')->default(0);
            $table->string('currency_code', 3)->default('NAD');
            $table->timestamps();
        });

        // Pivot: organisation ↔ users
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // member | org_admin
            $table->timestamps();

            $table->unique(['organisation_id', 'user_id']);
        });

        // FR-16.2 — Monthly invoices
        Schema::create('organisation_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->string('period');                      // e.g. "2026-03"
            $table->unsignedBigInteger('total_cents')->default(0);
            $table->string('currency_code', 3)->default('NAD');
            $table->string('status')->default('pending');  // pending | paid
            $table->timestamps();
        });

        // Add organisation_id to users for quick lookup
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organisation_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organisation_id']);
            $table->dropColumn('organisation_id');
        });

        Schema::dropIfExists('organisation_invoices');
        Schema::dropIfExists('organisation_user');
        Schema::dropIfExists('organisations');
    }
};
