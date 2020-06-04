<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantEventsTable extends Migration
{
	public function up()
	{
		Schema::create('plant_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->string('site');

			$table->uuid('planter_id')->index();
			$table->foreign('planter_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('plant_events');
	}
}
