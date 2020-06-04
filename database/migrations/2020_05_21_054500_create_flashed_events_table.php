<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlashedEventsTable extends Migration
{
	public function up()
	{
		Schema::create('flashed_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->string('flashbang_entity_id');
			$table->double('duration', 8, 4)->unsigned();
			$table->boolean('teamflash')->default(false);

			$table->uuid('attacker_id')->index();
			$table->foreign('attacker_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('victim_id')->index();
			$table->foreign('victim_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('flashed_events');
	}
}
