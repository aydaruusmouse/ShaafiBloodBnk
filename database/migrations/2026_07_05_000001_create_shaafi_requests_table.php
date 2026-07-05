<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shaafi_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->enum('request_type', ['donation', 'blood_request']);
            $table->string('full_name');
            $table->string('mobile_number');
            $table->string('blood_group');
            $table->unsignedTinyInteger('blood_quantity')->nullable();
            $table->string('city');
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->text('additional_notes')->nullable();
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'scheduled',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->text('agent_notes')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('shaafi_user_id')->nullable();
            $table->string('external_reference')->nullable()->unique();
            $table->timestamps();

            $table->index(['status', 'request_type']);
            $table->index(['hospital_id', 'status']);
            $table->index('mobile_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shaafi_requests');
    }
};
