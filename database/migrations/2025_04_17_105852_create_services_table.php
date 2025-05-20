<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('driving_style_id');
        $table->string('service_name');
        $table->date('service_date');
        $table->integer('service_mileage');
        $table->json('parts_list_id_cost'); // Array of part ID & cost
        $table->decimal('service_cost', 10, 2);
        $table->text('note')->nullable();
        $table->string('document')->nullable(); // File path
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('driving_style_id')->references('id')->on('driving_styles')->onDelete('restrict');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
