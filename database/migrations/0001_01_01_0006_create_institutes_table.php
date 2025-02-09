<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->id('institute_id');
            $table->string('instituteName');
            $table->string('instituteAddress');
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('subdistrict', 100);
            $table->string('zipcode', 5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutes');
    }
};
