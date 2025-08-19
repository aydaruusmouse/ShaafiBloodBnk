<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('sex', ['male', 'female']);
            $table->integer('age');
            $table->string('occupation');
            $table->string('village');
            $table->string('tell');
            $table->decimal('weight', 5, 2);
            $table->string('bp');
            $table->string('hemoglobin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
}; 