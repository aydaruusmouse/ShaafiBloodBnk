<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shaafi_requests', function (Blueprint $table) {
            $table->timestamp('sms_sent_at')->nullable()->after('reviewed_at');
            $table->string('sms_last_error')->nullable()->after('sms_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('shaafi_requests', function (Blueprint $table) {
            $table->dropColumn(['sms_sent_at', 'sms_last_error']);
        });
    }
};
