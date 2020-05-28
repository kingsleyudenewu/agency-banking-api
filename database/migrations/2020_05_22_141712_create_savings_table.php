<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->bigInteger('amount')->default(0);
            $table->bigInteger('target')->default(0);
            $table->uuid('saving_cycle_id')->index();
            $table->uuid('owner_id')->index();
            $table->uuid('creator_id')->index();
            $table->dateTime('completed')->nullable();
            $table->text('meta')->nullable();
            $table->date('maturity');
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
        Schema::dropIfExists('savings');
    }
}
