<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->foreignId('activity_type_id')->nullable()->constrained('activity_types', 'activity_type_id')->onDelete('set null');
            $table->string('activity_name');
            $table->text('description');
            $table->integer('children_price')->nullable()->default(0);
            $table->integer('student_price')->nullable()->default(0);
            $table->integer('adult_price')->nullable()->default(0);
            $table->integer('kid_price')->nullable()->default(0);
            $table->integer('disabled_price')->nullable()->default(0);
            $table->integer('elderly_price')->nullable()->default(0);
            $table->integer('monk_price')->nullable()->default(0);
            $table->integer('max_capacity')->nullable();
            $table->string('status')->default('active');
            $table->integer('duration_days')->nullable();
            $table->integer('max_subactivities')->default(0);
            $table->integer('target_yearly_count')->nullable()->default(0);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
