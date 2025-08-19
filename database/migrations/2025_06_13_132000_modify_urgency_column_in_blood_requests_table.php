<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // First drop the existing urgency column
            $table->dropColumn('urgency');
            
            // Then add it back as a string
            $table->string('urgency')->after('required_date');
        });
    }

    public function down()
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Drop the string column
            $table->dropColumn('urgency');
            
            // Add back the enum column
            $table->enum('urgency', ['low', 'medium', 'high'])->after('required_date');
        });
    }
}; 