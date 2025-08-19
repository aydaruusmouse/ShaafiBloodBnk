<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blood_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // whole blood, plasma, platelets, RBCs
            $table->string('code')->unique(); // WB, PL, PLT, RBC
            $table->text('description')->nullable();
            $table->integer('shelf_life_days');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blood_components');
    }
}; 