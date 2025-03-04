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
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('old_status');
            $table->tinyInteger('new_status');
            $table->text('comments')->nullable();
            $table->integer('actual_children_qty')->default(0);
            $table->integer('actual_students_qty')->default(0);
            $table->integer('actual_adults_qty')->default(0);
            $table->integer('actual_kid_qty')->default(0);
            $table->integer('actual_disabled_qty')->default(0);
            $table->integer('actual_elderly_qty')->default(0);
            $table->integer('actual_monk_qty')->default(0);
            $table->integer('actual_free_teachers_qty')->default(0);
            $table->string( 'changed_by');
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
