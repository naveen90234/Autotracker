<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('make');
            $table->string('model');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('cars');
    }
};
