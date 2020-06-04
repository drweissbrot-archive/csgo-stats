<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundsTable extends Migration
{
	public function up()
	{
		Schema::create('rounds', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('round_no')->unsigned();
			$table->boolean('is_counted')->default(false); // when false, the round will be ignored for all stats (e.g. for rounds that have been restarted by loading a backup)

			$table->integer('win_reason');
			$table->integer('win_side');

			$table->integer('team_a_survived')->unsigned();
			$table->integer('team_b_survived')->unsigned();

			$table->integer('team_a_side');
			$table->integer('team_b_side');

			$table->uuid('winner_team_id')->index()->nullable();
			$table->foreign('winner_team_id')->references('id')->on('teams')->onDelete('cascade');

			$table->uuid('mvp_id')->index()->nullable();
			$table->foreign('mvp_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('match_id')->index();
			$table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('rounds');
	}
}
