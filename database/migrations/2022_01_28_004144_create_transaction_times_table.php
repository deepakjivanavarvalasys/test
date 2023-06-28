<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_times', function (Blueprint $table) {
            $table->increments('id');

            $table->string('cd');
            $table->string('cdqa');
            $table->string('abm');
            $table->string('lead_nurture');
            $table->string('address_fetch');

            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();

            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('transaction_times');
    }
}
