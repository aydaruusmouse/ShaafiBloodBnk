<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure donors table has hospital_id for tenancy (fixes "Column not found: donors.hospital_id").
     */
    public function up(): void
    {
        if (!Schema::hasTable('donors')) {
            return;
        }

        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors', 'hospital_id')) {
                $table->foreignId('hospital_id')->nullable()->after('id')->constrained('hospitals')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('donors') || !Schema::hasColumn('donors', 'hospital_id')) {
            return;
        }

        Schema::table('donors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_id');
        });
    }
};
