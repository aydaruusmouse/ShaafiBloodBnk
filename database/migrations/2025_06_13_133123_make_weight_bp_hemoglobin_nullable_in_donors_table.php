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
        Schema::table('donors', function (Blueprint $table) {
            $table->float('weight')->nullable()->change();
            $table->string('bp')->nullable()->change();
            $table->string('hemoglobin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->float('weight')->nullable(false)->change();
            $table->string('bp')->nullable(false)->change();
            $table->string('hemoglobin')->nullable(false)->change();
        });
    }
};
