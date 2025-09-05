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
        Schema::table('routes', function (Blueprint $table) {
            // Add coordinate columns if they don't exist
            if (!Schema::hasColumn('routes', 'start_coordinates')) {
                $table->string('start_coordinates')->nullable()->after('start_location');
            }
            if (!Schema::hasColumn('routes', 'end_coordinates')) {
                $table->string('end_coordinates')->nullable()->after('end_location');
            }
            if (!Schema::hasColumn('routes', 'distance_km')) {
                $table->decimal('distance_km', 8, 2)->nullable()->after('end_coordinates');
            }
            if (!Schema::hasColumn('routes', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->after('distance_km');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['start_coordinates', 'end_coordinates', 'distance_km', 'estimated_duration']);
        });
    }
};