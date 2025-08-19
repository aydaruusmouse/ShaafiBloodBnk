<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('blood_requests', 'units_needed')) {
                $table->integer('units_needed')->after('blood_group');
            }
            if (!Schema::hasColumn('blood_requests', 'patient_name')) {
                $table->string('patient_name')->after('units_needed');
            }
            if (!Schema::hasColumn('blood_requests', 'required_date')) {
                $table->dateTime('required_date')->after('patient_name');
            }
            if (!Schema::hasColumn('blood_requests', 'urgency')) {
                $table->string('urgency')->after('required_date');
            }
            if (!Schema::hasColumn('blood_requests', 'notes')) {
                $table->text('notes')->nullable()->after('urgency');
            }
        });
    }

    public function down()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn(['units_needed', 'patient_name', 'required_date', 'urgency', 'notes']);
        });
    }
}; 