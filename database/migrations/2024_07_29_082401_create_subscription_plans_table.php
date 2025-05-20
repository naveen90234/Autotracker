<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('plan_id');
            $table->decimal('amount', 20, 2);
            $table->string('slug', 105);
            $table->integer('time_limit');
            $table->enum('duration', ['day', 'week', 'month', 'year']);
            $table->text('description');
            $table->boolean('status')->default(1)->comment("0= Inactive, 1= Active");
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
        Schema::dropIfExists('subscription_plans');
    }
}
