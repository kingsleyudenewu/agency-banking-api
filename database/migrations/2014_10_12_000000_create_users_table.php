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
            $table->uuid('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('other_name')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified')->nullable();
            $table->string('email', 150)->unique();
            $table->string('phone', 150)->unique();
            $table->string('password');
            $table->uuid('parent_id')->nullable();
            $table->string('api_token')
                ->unique()
                ->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('last_login')->nullable();
            $table->primary('id');
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
