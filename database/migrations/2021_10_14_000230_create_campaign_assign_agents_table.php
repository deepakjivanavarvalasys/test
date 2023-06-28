<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignAssignAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_assign_agents', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('campaign_assign_ratl_id');
            $table->foreign('campaign_assign_ratl_id')->references('id')->on('campaign_assign_r_a_t_l_s')->onUpdate('cascade');

            $table->unsignedInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade');

            $table->unsignedInteger('agent_work_type_id');
            $table->foreign('agent_work_type_id')->references('id')->on('agent_work_types');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');

            $table->integer('accounts_utilized')->default(0);

            $table->date('display_date')->nullable();
            $table->integer('allocation')->default(1);
            $table->string('reporting_file')->nullable();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();

            $table->integer('assigned_by');
            $table->tinyInteger('status')->default(1)->comment('1-active,0-inactive,2-revoke');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_At')->useCurrent();
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
        Schema::dropIfExists('campaign_assign_agents');
    }
}
