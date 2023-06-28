<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->increments('id');

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
            $table->string('revenue', 50);
            $table->string('company_domain', 100);
            $table->string('website')->nullable();
            $table->text('company_linkedin_url')->nullable();
            $table->text('linkedin_profile_link');
            $table->text('linkedin_profile_sn_link');
            //---Lead Details

            $table->unsignedInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade');

            $table->tinyInteger('status')->comment('1-Available,2-Used');

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
        Schema::dropIfExists('data');
    }
}
