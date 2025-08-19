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
        Schema::create('blood_bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('blood_group');
            $table->enum('component_type', ['whole_blood', 'rbc', 'plasma', 'platelets']);
            $table->decimal('volume', 5, 2);
            $table->date('collection_date');
            $table->date('expiry_date');
            $table->enum('status', ['available', 'reserved', 'transfused', 'expired', 'discarded']);
            $table->string('collected_by');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_bags');
    }
};
