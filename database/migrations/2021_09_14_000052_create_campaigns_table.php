<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('campaign_id', '50');
            $table->string('v_mail_campaign_id', '50')->nullable();

            $table->unsignedInteger('campaign_filter_id');
            $table->foreign('campaign_filter_id')->references('id')->on('campaign_filters')->onUpdate('cascade');

            $table->unsignedInteger('campaign_type_id');
            $table->foreign('campaign_type_id')->references('id')->on('campaign_types')->onUpdate('cascade');

            $table->longText('note')->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->integer('allocation');
            $table->integer('deliver_count')->default(0);
            $table->integer('shortfall_count')->default(0);

            $table->unsignedInteger('campaign_status_id');
            $table->foreign('campaign_status_id')->references('id')->on('campaign_statuses')->onUpdate('cascade');

            $table->enum('pacing', ['Daily', 'Monthly', 'Weekly']);

            $table->string('type')->default('new')->comment('new,reactivated,incremental');
            $table->tinyInteger('status')->default('1')->comment('1-Active, 0-Inactive');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::table('campaigns', function (Blueprint $table){
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('campaigns')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
