<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_leads', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ca_agent_id');
            $table->foreign('ca_agent_id')->references('id')->on('campaign_assign_agents')->onUpdate('cascade');

            $table->unsignedInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade');

            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('users')->onUpdate('cascade');

            $table->unsignedInteger('transaction_time_id');
            $table->foreign('transaction_time_id')->references('id')->on('transaction_times');

            //Lead Details---
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('company_name');
            $table->string('email_address');
            $table->string('specific_title');
            $table->string('job_level')->nullable();
            $table->string('job_role')->nullable();
            $table->string('phone_number', 20);
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('zipcode', 10);
            $table->string('country', 100);
            $table->string('industry');
            $table->string('employee_size', 50);
            $table->string('employee_size_2', 50)->nullable();
            $table->string('revenue', 50);
            $table->string('company_domain', 100);
            $table->string('website')->nullable();
            $table->text('company_linkedin_url')->nullable();
            $table->text('linkedin_profile_link');
            $table->text('linkedin_profile_sn_link')->nullable();
            $table->text('comment')->nullable();
            $table->text('comment_2')->nullable();
            $table->text('qc_comment')->nullable();
            //---Lead Details

            $table->tinyInteger('status')->default(1);
            $table->dateTime('send_date')->nullable();
            $table->dateTime('qc_download_date')->nullable();
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
        Schema::dropIfExists('agent_leads');
    }
}
