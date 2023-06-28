<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEMENotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_m_e_notifications', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedInteger('recipient_id');
            $table->foreign('recipient_id')->references('id')->on('users')->onUpdate('cascade');

            $table->tinyInteger('read_status')->default(0);
            $table->text('message');
            $table->text('url')->nullable();

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
        Schema::dropIfExists('e_m_e_notifications');
    }
}
