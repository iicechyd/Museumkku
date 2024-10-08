<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('timeslots_id');
            $table->date('booking_date');
            $table->string('instituteName');
            $table->string('instituteAddress');
            $table->string('province');
            $table->string('district');
            $table->string('subdistrict');
            $table->string('zip', 5);
            $table->string('visitorName');
            $table->string('visitorEmail');
            $table->string('tel', 10);
            $table->integer('children_qty')->default(0);
            $table->integer('students_qty')->default(0);
            $table->integer('adults_qty')->default(0);
            $table->tinyInteger('status')->default(0);

            $table->foreign('activity_id')->references('activity_id')->on('activities')->onDelete('cascade');
            $table->foreign('timeslots_id')->references('timeslots_id')->on('timeslots')->onDelete('cascade');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
