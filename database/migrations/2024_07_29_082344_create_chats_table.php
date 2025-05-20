<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender')->nullable()->index('dlt_sender');
            $table->unsignedInteger('receiver')->nullable()->index('dlt_receiver');
            $table->text('message')->nullable();
            $table->enum('message_type', ['TEXT', 'IMAGE', 'VIDEO', 'CLEARED'])->nullable();
            $table->string('group_code', 10)->nullable();
            $table->string('group_type', 100)->default('SINGLE');
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_seen')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->integer('deleted_by')->default(0);
            $table->integer('blocked_user')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
