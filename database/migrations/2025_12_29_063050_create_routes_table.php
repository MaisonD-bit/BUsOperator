<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('start_location');
            $table->string('end_location');
            $table->string('start_coordinates')->nullable();
            $table->string('end_coordinates')->nullable();
            $table->text('stops_data')->nullable();
            $table->text('description')->nullable();
            
            // ✅ Combined fare fields (keep for backward compatibility + mobile API)
            $table->decimal('regular_price', 8, 2)->nullable(); // Optional: can be removed later
            $table->decimal('aircon_price', 8, 2)->nullable();  // Optional: can be removed later
            
            // ✅ New primary fare field (used in web panel)
            $table->decimal('route_fare', 8, 2)->nullable(); // Calculated based on bus_type + distance

            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // ✅ Bus type (critical for fare logic)
            $table->enum('bus_type', ['regular', 'aircon'])->default('regular');
            
            $table->text('geometry')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('stop_id')->constrained()->onDelete('cascade');
            $table->integer('stop_order');
            $table->integer('estimated_minutes')->default(0);
            $table->timestamps();
            $table->unique(['route_id', 'stop_id', 'stop_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('routes');
    }
};