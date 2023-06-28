<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_data', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ca_agent_id');
            $table->foreign('ca_agent_id')->references('id')->on('campaign_assign_agents')->onUpdate('cascade');


            $table->unsignedInteger('data_id');
            $table->foreign('data_id')->references('id')->on('data')->onUpdate('cascade');

            $table->tinyInteger('status')->default(1);


            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_At')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_data');
    }
}
