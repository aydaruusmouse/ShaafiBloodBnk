<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Drop any existing columns that might be causing issues
            if (Schema::hasColumn('departments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('departments', 'floor')) {
                $table->dropColumn('floor');
            }
            if (Schema::hasColumn('departments', 'room_number')) {
                $table->dropColumn('room_number');
            }
            if (Schema::hasColumn('departments', 'contact_number')) {
                $table->dropColumn('contact_number');
            }

            // Add or modify required columns
            if (!Schema::hasColumn('departments', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('departments', 'hospital_id')) {
                $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('departments', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('departments', 'head_of_department')) {
                $table->string('head_of_department');
            }
            if (!Schema::hasColumn('departments', 'phone')) {
                $table->string('phone');
            }
            if (!Schema::hasColumn('departments', 'email')) {
                $table->string('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // No need to reverse these changes as they are structural fixes
        });
    }
};
