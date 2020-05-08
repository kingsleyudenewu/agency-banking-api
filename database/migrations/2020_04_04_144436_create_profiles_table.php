<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('address')->nullable();
            $table->string('passport')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->enum('marital_status', ['married', 'single', 'unknown'])->default('unknown');
            $table->string('lga')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_phone', 40)->nullable();
            $table->string('bvn', 30)->nullable();
            $table->uuid('state_id')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('agreement_form')->nullable();
            $table->boolean('setup_completed')->default(false);
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->uuid('user_id');
            $table->primary('id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
