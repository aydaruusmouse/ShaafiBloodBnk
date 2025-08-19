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
        Schema::table('hospitals', function (Blueprint $table) {
            // Remove unused columns that are no longer in the model
            $table->dropColumn([
                'contact_person',
                'notes', 
                'country',
                'contact_phone',
                'type'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            // Re-add the columns if we need to rollback
            $table->string('contact_person')->nullable();
            $table->text('notes')->nullable();
            $table->string('country')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('type')->nullable();
        });
    }
};
