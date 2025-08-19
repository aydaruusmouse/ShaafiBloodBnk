<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add the lab role if not exists
        if (!DB::table('roles')->where('name', 'lab')->exists()) {
            DB::table('roles')->insert([
                'name' => 'lab',
                'description' => 'Laboratory Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove the lab role
        DB::table('roles')->where('name', 'lab')->delete();
    }
}; 