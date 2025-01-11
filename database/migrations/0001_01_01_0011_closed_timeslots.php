<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('closed_timeslots', function (Blueprint $table) {
            $table->id('closed_timeslots_id');
            $table->foreignId('activity_id')->constrained('activities', 'activity_id')->onDelete('cascade');
            $table->foreignId('timeslots_id')->nullable()->constrained('timeslots', 'timeslots_id')->onDelete('cascade');
            $table->text('comments')->nullable();

            $table->date('closed_on');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closed_timeslots');

    }
};
