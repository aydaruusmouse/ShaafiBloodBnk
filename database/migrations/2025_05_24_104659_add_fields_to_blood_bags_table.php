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
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->string('donor_type')->default('volunteer')->after('donor_id');
            $table->foreignId('patient_id')->nullable()->after('donor_id')->constrained('patients');
            $table->string('collection_location')->nullable()->after('collected_by');
            
            // Add indexes for better performance
            $table->index('status');
            $table->index('expiry_date');
            $table->index('blood_group');
            $table->index('component_type');
            $table->index('donor_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_bags', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['patient_id']);
            
            // Drop columns
            $table->dropColumn([
                'donor_type',
                'patient_id',
                'collection_location'
            ]);
            
            // Drop indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['expiry_date']);
            $table->dropIndex(['blood_group']);
            $table->dropIndex(['component_type']);
            $table->dropIndex(['donor_type']);
        });
    }
};