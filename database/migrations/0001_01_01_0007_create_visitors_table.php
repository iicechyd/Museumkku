<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id('visitor_id');
            $table->unsignedBigInteger('institute_id');
            $table->string('visitorName');
            $table->string('visitorEmail')->unique();
            $table->string('tel', 10);
            $table->foreign('institute_id')->references('institute_id')->on('institutes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
