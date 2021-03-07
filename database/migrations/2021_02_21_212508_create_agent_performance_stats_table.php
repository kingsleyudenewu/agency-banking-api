<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPerformanceStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_performance_stats', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->bigInteger('saving_volume')->default(0);
            $table->bigInteger('saving_value')->default(0);
            $table->bigInteger('customer_acquired')->default(0);
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
        Schema::dropIfExists('agent_performance_stats');
    }
}
