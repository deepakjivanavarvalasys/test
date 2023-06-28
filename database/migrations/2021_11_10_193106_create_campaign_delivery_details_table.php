<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_delivery_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade');

            $table->integer('lead_sent')->default(0);
            $table->integer('lead_approved')->default(0);
            $table->integer('lead_rejected')->default(0);
            $table->integer('lead_available')->default(0);
            $table->integer('lead_pending')->default(0);
            $table->string('campaign_progress')->default('Campaign IN');

            $table->unsignedInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
        Schema::dropIfExists('campaign_delivery_details');
    }
}
