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
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'age')) {
                $table->integer('age')->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('patients', 'gender')) {
                $table->string('gender', 20)->nullable()->after('age');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('patients', 'age')) {
                $table->dropColumn('age');
            }
        });
    }
};


