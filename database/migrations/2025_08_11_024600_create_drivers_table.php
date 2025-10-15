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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            
            // Basic driver information
            $table->string('name');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->text('address');
            $table->date('date_of_birth');
            $table->string('gender');
            
            // License information
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            
            // Emergency contact
            $table->string('emergency_name');
            $table->string('emergency_relation');
            $table->string('emergency_contact');
            
            // Driver status and assignment
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('terminal')->default('North Terminal');
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->string('photo_url')->nullable();
            
            // External reference (from Bus Driver app)
            $table->string('external_driver_id')->nullable()->unique(); // Reference to driver ID from Bus Driver app
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
