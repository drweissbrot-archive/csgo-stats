<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKillEventsTable extends Migration
{
	public function up()
	{
		Schema::create('kill_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->string('weapon');
			$table->boolean('headshot');

			$table->boolean('through_wall');
			$table->boolean('noscope');
			$table->boolean('through_smoke');
			$table->boolean('attacker_flashed');

			$table->boolean('teamkill')->default(false);
			$table->boolean('team_assist')->default(false);
			$table->boolean('flash_assist');

			$table->uuid('attacker_id')->index();
			$table->foreign('attacker_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('victim_id')->index();
			$table->foreign('victim_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('assister_id')->index()->nullable();
			$table->foreign('assister_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('kill_events');
	}
}
