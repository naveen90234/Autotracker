<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionControlSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version_control_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('field_title');
            $table->string('field_name');
            $table->enum('field_type', ['text', 'image', 'number', 'url', 'date', 'email', 'checkbox']);
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('version_control_settings');
    }
}
