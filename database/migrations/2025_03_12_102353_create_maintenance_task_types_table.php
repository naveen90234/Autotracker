<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('maintenance_task_types', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Task Type Name
            $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });

        Schema::create('maintenance_task_type_car_part', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_task_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_part_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_task_type_car_part');
        Schema::dropIfExists('maintenance_task_types');
    }
};
