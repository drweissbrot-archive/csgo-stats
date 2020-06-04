<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
	public function up()
	{
		Schema::create('matches', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_series')->unsigned();
			$table->boolean('is_knife_round');

			$table->integer('round_count')->unsigned();
			$table->integer('max_rounds')->unsigned();
			$table->boolean('has_halftime');

			$table->integer('team_a_score')->unsigned();
			$table->integer('team_a_score_first_half')->unsigned();
			$table->integer('team_a_score_second_half')->unsigned();
			$table->integer('team_a_score_ot')->unsigned()->nullable();

			$table->integer('team_b_score')->unsigned();
			$table->integer('team_b_score_first_half')->unsigned();
			$table->integer('team_b_score_second_half')->unsigned();
			$table->integer('team_b_score_ot')->unsigned()->nullable();

			$table->string('team_a_started_on');
			$table->string('team_b_started_on');

			$table->string('server_name');
			$table->integer('tickrate')->unsigned();
			$table->integer('ticks')->unsigned();
			$table->integer('duration')->unsigned();

			$table->dateTime('started_at');
			$table->string('demo_path')->nullable();
			$table->text('notes')->nullable();

			$table->integer('game_mode');
			$table->integer('game_type');

			$table->uuid('winner_team_id')->index()->nullable();
			$table->foreign('winner_team_id')->references('id')->on('teams')->onDelete('cascade');

			$table->uuid('map_id')->index();
			$table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');

			$table->uuid('series_id')->index();
			$table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('matches');
	}
}
