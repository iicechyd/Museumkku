<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('closed_tmss', function (Blueprint $table) {
            $table->id('closed_tmss_id');
            $table->foreignId('activity_id')->constrained('activities', 'activity_id')->onDelete('cascade');
            $table->foreignId('tmss_id')->nullable()->constrained('tmss', 'tmss_id')->onDelete('cascade');
            $table->text('comments')->nullable();

            $table->date('closed_on');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closed_tmss');

    }
};
