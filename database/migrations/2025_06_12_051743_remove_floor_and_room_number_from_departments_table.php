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
            if (Schema::hasColumn('departments', 'floor')) {
                $table->dropColumn('floor');
            }
            if (Schema::hasColumn('departments', 'room_number')) {
                $table->dropColumn('room_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'floor')) {
                $table->string('floor')->nullable();
            }
            if (!Schema::hasColumn('departments', 'room_number')) {
                $table->string('room_number')->nullable();
            }
        });
    }
};
