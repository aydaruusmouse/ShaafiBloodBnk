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
        Schema::table('transfusions', function (Blueprint $table) {
            // Add new columns if they do not exist
            if (!Schema::hasColumn('transfusions', 'transfusion_date')) {
                $table->dateTime('transfusion_date')->after('patient_id');
            }
            if (!Schema::hasColumn('transfusions', 'reason')) {
                $table->string('reason')->after('transfusion_date');
            }
            if (!Schema::hasColumn('transfusions', 'notes')) {
                $table->text('notes')->nullable()->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfusions', function (Blueprint $table) {
            // Drop new columns
            if (Schema::hasColumn('transfusions', 'transfusion_date')) {
                $table->dropColumn('transfusion_date');
            }
            if (Schema::hasColumn('transfusions', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('transfusions', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
