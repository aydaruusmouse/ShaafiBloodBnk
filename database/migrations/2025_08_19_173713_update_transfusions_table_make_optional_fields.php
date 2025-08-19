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
        Schema::table('transfusions', function (Blueprint $table) {
            // Drop the foreign key constraints first
            $table->dropForeign(['blood_request_id']);
            $table->dropForeign(['department_id']);
            
            // Make the columns nullable
            $table->foreignId('blood_request_id')->nullable()->change();
            $table->foreignId('department_id')->nullable()->change();
            
            // Re-add foreign key constraints with nullable
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfusions', function (Blueprint $table) {
            // Drop the foreign key constraints
            $table->dropForeign(['blood_request_id']);
            $table->dropForeign(['department_id']);
            
            // Make the columns required again
            $table->foreignId('blood_request_id')->nullable(false)->change();
            $table->foreignId('department_id')->nullable(false)->change();
            
            // Re-add foreign key constraints
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }
};
