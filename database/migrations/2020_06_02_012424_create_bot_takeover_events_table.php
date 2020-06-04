<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotTakeoverEventsTable extends Migration
{
	public function up()
	{
		Schema::create('bot_takeover_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->uuid('human_id')->index();
			$table->foreign('human_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('bot_id')->index();
			$table->foreign('bot_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('bot_takeover_events');
	}
}
