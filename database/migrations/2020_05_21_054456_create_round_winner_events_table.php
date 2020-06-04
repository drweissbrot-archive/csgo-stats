<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundWinnerEventsTable extends Migration
{
	public function up()
	{
		Schema::create('round_winner_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->integer('winner_side');
			$table->integer('reason');

			$table->uuid('winner_team_id')->index();
			$table->foreign('winner_team_id')->references('id')->on('teams')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('round_winner_events');
	}
}
