<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoggyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loggy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('app_id');
            $table->integer('user_id')->nullable();
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loggy');
    }
}
