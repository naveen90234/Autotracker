<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }
    
}
