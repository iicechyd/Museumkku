<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_subactivities', function (Blueprint $table) {
            $table->id('booking_subactivity_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('sub_activity_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('sub_activity_id')->references('sub_activity_id')->on('sub_activities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_subactivities');
    }
};
