<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesTeamTable extends Migration
{
	public function up()
	{
		Schema::create('series_team', function (Blueprint $table) {
			$table->id();
			$table->timestamps();

			$table->string('letter');

			$table->uuid('series_id')->index();
			$table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');

			$table->uuid('team_id')->index();
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('series_team');
	}
}
