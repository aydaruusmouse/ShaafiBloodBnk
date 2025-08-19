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
            if (Schema::hasColumn('hospitals', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('hospitals', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('hospitals', 'bed_capacity')) {
                $table->dropColumn('bed_capacity');
            }
            if (Schema::hasColumn('hospitals', 'license_number')) {
                $table->dropColumn('license_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'bed_capacity')) {
                $table->integer('bed_capacity')->nullable();
            }
            if (!Schema::hasColumn('hospitals', 'license_number')) {
                $table->string('license_number')->nullable();
            }
        });
    }
};
