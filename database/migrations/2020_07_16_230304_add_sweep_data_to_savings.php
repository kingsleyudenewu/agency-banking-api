<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSweepDataToSavings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->enum('sweep_status', ['processing', 'swept'])->nullable();
            $table->text('sweep_comment')->nullable();
            $table->dateTime('swept_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->dropColumn(['sweep_status', 'sweep_comment', 'swept_at']);
        });
    }
}
