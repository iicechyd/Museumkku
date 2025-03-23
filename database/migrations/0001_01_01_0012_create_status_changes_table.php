<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_changes', function (Blueprint $table) {
            $table->id('changed_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->tinyInteger('old_status');
            $table->tinyInteger('new_status');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_changes');
    }
};
