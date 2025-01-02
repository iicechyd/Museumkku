<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('changed_id')->nullable();
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('changed_id')->references('changed_id')->on('status_changes')->onDelete('cascade');
        
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
    }
};
