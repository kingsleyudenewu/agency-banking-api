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
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('bank_account_number')->nullable();
            $table->uuid('bank_id')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->enum('marital_status', ['married', 'single', 'unknown'])->nullable();
            $table->uuid('state_id')->nullable();
            $table->string('lga')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_phone', 40)->nullable();
            $table->string('bvn', 30)->nullable();
            $table->string('means_of_identification')->nullable();
            $table->string('application_form')->nullable();
            $table->string('agreement_form')->nullable();
            $table->boolean('setup_completed')->default(false);
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->integer('commission')->default(0);
            $table->integer('commission_for_agent')->default(0);
            $table->boolean('has_bank_account')->default(false);
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
