<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('sub_activities', function (Blueprint $table) {
            $table->id('sub_activity_id');
            $table->foreignId('activity_id')->constrained('activities', 'activity_id')->onDelete('cascade');
            $table->string('sub_activity_name');
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_activities');
    }
};
