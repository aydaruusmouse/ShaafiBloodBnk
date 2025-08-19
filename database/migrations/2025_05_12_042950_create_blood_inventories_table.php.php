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
        Schema::create('blood_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('bag_serial')->unique();
            $table->enum('component', ['whole_blood','rbc','plasma','platelets']);
            $table->string('blood_group'); // e.g. A+, O-
            $table->date('collected_at');
            $table->date('expires_at');
            $table->enum('status',['available','reserved','used','expired'])->default('available');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
