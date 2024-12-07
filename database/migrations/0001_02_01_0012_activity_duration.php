<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_durations', function (Blueprint $table) {
            $table->id('activity_duration_id');
            $table->foreignId('activity_id')->constrained('activities', 'activity_id')->onDelete('cascade');
            $table->integer('duration_days');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_durations');
    }
};
