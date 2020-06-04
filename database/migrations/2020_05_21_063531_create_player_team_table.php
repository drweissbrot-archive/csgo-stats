<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerTeamTable extends Migration
{
	public function up()
	{
		Schema::create('player_team', function (Blueprint $table) {
			$table->id();
			$table->timestamps();

			$table->uuid('player_id')->index();
			$table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('team_id')->index();
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('player_teams');
	}
}
