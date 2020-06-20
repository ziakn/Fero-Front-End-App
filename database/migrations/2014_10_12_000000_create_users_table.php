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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('user_type')->nullable();
            $table->string('restaurent_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('iban')->nullable();
            $table->text('address')->nullable();
            $table->text('restaurent_address')->nullable();
            $table->string('contact')->nullable();
            $table->double('lat')->nullable();
            $table->double('lon')->nullable();
            $table->string('image')->nullable()->default('/profile.png');
            $table->rememberToken();
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
