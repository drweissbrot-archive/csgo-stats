<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMolotovDetonatedEventsTable extends Migration
{
	public function up()
	{
		Schema::create('molotov_detonated_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->uuid('thrower_id')->index();
			$table->foreign('thrower_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('molotov_detonated_events');
	}
}
