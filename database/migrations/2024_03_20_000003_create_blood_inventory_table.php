<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blood_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('blood_group'); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->foreignId('component_id')->constrained('blood_components');
            $table->foreignId('donor_id')->nullable()->constrained('donors');
            $table->foreignId('storage_location_id')->constrained('storage_locations');
            $table->string('status')->default('available'); // available, reserved, used, expired
            $table->date('collection_date');
            $table->date('expiry_date');
            $table->string('barcode')->unique();
            $table->text('notes')->nullable();
            $table->string('test_status')->default('pending'); // pending, passed, failed
            $table->date('test_date')->nullable();
            $table->string('tested_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blood_inventory');
    }
}; 