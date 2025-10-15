<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('bus_id')->constrained()->onDelete('cascade');
            
            // Updated status enum to include all possible statuses
            $table->enum('status', ['scheduled', 'accepted', 'declined', 'active', 'completed', 'cancelled'])->default('scheduled');
            
            $table->decimal('fare_regular', 8, 2)->default(0);
            $table->decimal('fare_aircon', 8, 2)->default(0);
            $table->string('terminal_space', 10)->nullable();
            $table->text('notes')->nullable();

            // Existing columns from seeder
            $table->json('actual_stops')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('passengers')->nullable();

            // Add status timestamp columns
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};