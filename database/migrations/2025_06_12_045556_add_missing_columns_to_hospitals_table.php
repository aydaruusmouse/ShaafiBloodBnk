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
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'bed_capacity')) {
                $table->integer('bed_capacity')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'license_number')) {
                $table->string('license_number')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'type')) {
                $table->string('type')->default('public');
            }
            if (!Schema::hasColumn('hospitals', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (Schema::hasColumn('hospitals', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('hospitals', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('hospitals', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('hospitals', 'bed_capacity')) {
                $table->dropColumn('bed_capacity');
            }
            if (Schema::hasColumn('hospitals', 'license_number')) {
                $table->dropColumn('license_number');
            }
            if (Schema::hasColumn('hospitals', 'contact_person')) {
                $table->dropColumn('contact_person');
            }
            if (Schema::hasColumn('hospitals', 'contact_phone')) {
                $table->dropColumn('contact_phone');
            }
            if (Schema::hasColumn('hospitals', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('hospitals', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
