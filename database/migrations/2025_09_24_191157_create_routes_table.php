<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
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
            $table->decimal('regular_price', 8, 2);
            $table->decimal('aircon_price', 8, 2)->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropForeign(['route_id']);
            });
        }
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('routes');
    }
}