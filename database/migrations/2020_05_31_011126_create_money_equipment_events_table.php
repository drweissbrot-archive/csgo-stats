<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneyEquipmentEventsTable extends Migration
{
	public function up()
	{
		Schema::create('money_equipment_events', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->integer('index_within_round')->unsigned();
			$table->integer('tick')->unsigned();

			$table->integer('money_remaining')->unsigned();
			$table->integer('equipment_value')->unsigned();

			$table->uuid('player_id')->index();
			$table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');

			$table->uuid('round_id')->index();
			$table->foreign('round_id')->references('id')->on('rounds')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('money_equipment_events');
	}
}
