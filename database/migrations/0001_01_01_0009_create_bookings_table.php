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
            $table->unsignedBigInteger('tmss_id')->nullable();
            $table->unsignedBigInteger('visitor_id');
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('sub_activity_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('booking_date');
            $table->integer('children_qty')->default(0);
            $table->integer('students_qty')->default(0);
            $table->integer('adults_qty')->default(0);
            $table->integer('kid_qty')->default(0);
            $table->integer('disabled_qty')->default(0);
            $table->integer('elderly_qty')->default(0);
            $table->integer('monk_qty')->default(0);
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('activity_id')->references('activity_id')->on('activities')->onDelete('cascade');
            $table->foreign('sub_activity_id')->references('sub_activity_id')->on('sub_activities')->onDelete('set null');
            $table->foreign('tmss_id')->references('tmss_id')->on('tmss')->onDelete('cascade');
            $table->foreign('visitor_id')->references('visitor_id')->on('visitors')->onDelete('cascade');
            $table->foreign('institute_id')->references('institute_id')->on('institutes')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
