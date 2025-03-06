<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('tmss', function (Blueprint $table) {
            $table->id('tmss_id');
            $table->foreignId('activity_id')->constrained('activities', 'activity_id')->onDelete('cascade'); // Foreign Key
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmss');
    }
};
