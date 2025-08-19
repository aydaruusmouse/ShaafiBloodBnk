<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUnitsFieldInBloodRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Drop the units_needed column if it exists
            if (Schema::hasColumn('blood_requests', 'units_needed')) {
                $table->dropColumn('units_needed');
            }
            
            // Add units_required column if it doesn't exist
            if (!Schema::hasColumn('blood_requests', 'units_required')) {
                $table->integer('units_required')->after('blood_group');
            }
        });
    }

    public function down()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Drop units_required
            $table->dropColumn('units_required');
            
            // Add back units_needed
            $table->integer('units_needed')->after('blood_group');
        });
    }
} 