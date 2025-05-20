<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('plan_id');
            $table->decimal('amount', 20, 2);
            $table->enum('platform', ['IOS', 'ANDROID'])->default('IOS');
            $table->longText('receipt')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date');
            $table->boolean('status')->default(1)->comment("0=Expired, 1 = Active");
            $table->timestamps();

            $table->foreign('user_id', 'delete_subscription_payments')->references('id')->on('users')->onDelete('cascade')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_payments');
    }
}
