<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('status')->default(\App\CommissionPayout::STATUS_PENDING);
            $table->bigInteger('amount');
            $table->dateTime('paid')->nullable();
            $table->uuid('completed_by')->nullable();
            $table->bigInteger('wallet_id');
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
        Schema::dropIfExists('commission_payouts');
    }
}
