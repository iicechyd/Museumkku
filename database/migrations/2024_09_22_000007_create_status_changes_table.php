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
        Schema::create('status_changes', function (Blueprint $table) {
            $table->id('changed_id');
            $table->unsignedBigInteger('booking_id');
            $table->tinyInteger('old_status')->nullable(); // ค่าเก่าของสถานะ
            $table->tinyInteger('new_status')->nullable(); // ค่าใหม่ของสถานะ
            $table->timestamp('changed_at')->useCurrent();
            $table->text('comments')->nullable();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_changes');
    }
};
