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
            $table->tinyInteger('old_status');
            $table->tinyInteger('new_status');
            $table->text('comments')->nullable();
            $table->integer('number_of_visitors')->nullable();
            $table->string(column: 'changed_by')->nullable();
            $table->timestamp('status_updated_at');

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_changes');
    }
};
