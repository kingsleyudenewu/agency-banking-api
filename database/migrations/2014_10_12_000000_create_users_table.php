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
            $table->string('account_number', 10)->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified')->nullable();
            $table->string('email', 150)->unique();
            $table->string('phone', 150)->unique();
            $table->string('password');
            $table->uuid('parent_id')->nullable();
            $table->string('status')->default('approved');
            $table->uuid('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('approval_remark')->nullable();
            $table->string('api_token')
                ->unique()
                ->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('last_login')->nullable();
            $table->primary('id');
            $table->dateTime('verified_at')->nullable();
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
