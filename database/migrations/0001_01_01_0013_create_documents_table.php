<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->unsignedBigInteger('booking_id');
            $table->string('file_path')->nullable();
            $table->string('file_name');

            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
