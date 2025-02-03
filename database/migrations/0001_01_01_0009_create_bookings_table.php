<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('timeslots_id')->nullable();
            $table->unsignedBigInteger('visitor_id');
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('sub_activity_id')->nullable();
            $table->date('booking_date');
            $table->integer('children_qty')->default(0);
            $table->integer('students_qty')->default(0);
            $table->integer('adults_qty')->default(0);
            $table->integer('disabled_qty')->default(0);
            $table->integer('elderly_qty')->default(0);
            $table->integer('monk_qty')->default(0);
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(0);

            $table->foreign('activity_id')->references('activity_id')->on('activities')->onDelete('cascade');
            $table->foreign('sub_activity_id')->references('sub_activity_id')->on('sub_activities')->onDelete('set null');
            $table->foreign('timeslots_id')->references('timeslots_id')->on('timeslots')->onDelete('cascade');
            $table->foreign('visitor_id')->references('visitor_id')->on('visitors')->onDelete('cascade');
            $table->foreign('institute_id')->references('institute_id')->on('institutes')->onDelete('cascade');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
