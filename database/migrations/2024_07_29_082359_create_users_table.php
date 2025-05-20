<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 150)->nullable();
            $table->string('email')->unique('users_email_unique');
            $table->string('country_code_name', 150)->nullable();
            $table->string('country_code', 100)->nullable();
            $table->string('mobile', 100)->nullable();
            $table->string('mobile_number', 150)->nullable()->comment("Mobile number with country code");
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('address', 666)->nullable();
            $table->text('bio')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('status')->default(0)->comment("0= Inacive, 1= Active");
            $table->boolean('location_status')->default(1)->comment("0= Inactive, 1= Active");
            $table->boolean('notification_status')->default(1)->comment("0= Inactive, 1= Active");
            $table->string('latitude', 150)->nullable();
            $table->string('longitude', 150)->nullable();
            $table->boolean('otp_verify')->default(0)->comment("0= Not verify,1= Verify");
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_two_factor')->default(0)->comment("0= Off, 1= On");
            $table->boolean('online')->default(0);
            $table->string('plan_id')->nullable();
            $table->enum('is_premium', ['0', '1'])->default('0')->comment("0 = free, 1 = paid	");
            $table->boolean('is_free_plan')->default(1)->comment("1 => Free plan active, 0 => Free plan expire");
            $table->boolean('is_subscription_expired')->default(0)->comment("0=not expired,1=expired");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
