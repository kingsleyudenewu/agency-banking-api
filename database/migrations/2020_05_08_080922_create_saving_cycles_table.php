<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saving_cycles', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('title');
            $table->unsignedInteger('duration')->default(30);
            $table->string('description')->nullable();
            $table->string('rule')->default('DefaultLoanRule');
            $table->unsignedInteger('min_saving_frequent');
            $table->unsignedBigInteger('min_saving_amount')->comment('min amount to save daily');
            $table->enum('charge_type', ['flat', 'percent'])->default('flat');
            $table->integer('percentage_to_charge')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('saving_cycles');
    }
}


