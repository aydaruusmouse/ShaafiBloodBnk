<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // cold room, freezer, etc.
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('temperature', 5, 2); // in Celsius
            $table->string('status')->default('active'); // active, inactive, maintenance
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage_locations');
    }
}; 