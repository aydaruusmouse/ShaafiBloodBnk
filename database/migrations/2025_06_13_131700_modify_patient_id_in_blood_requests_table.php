<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Make patient_id nullable
            $table->unsignedBigInteger('patient_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Make patient_id required again
            $table->unsignedBigInteger('patient_id')->nullable(false)->change();
        });
    }
}; 