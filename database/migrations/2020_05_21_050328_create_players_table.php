<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
	public function up()
	{
		Schema::create('players', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->string('steam_id')->index()->unique();
			$table->boolean('bot')->default(false);

			$table->string('display_name');
			$table->string('steam_name');

			$table->string('flag')->nullable();
			$table->string('avatar_url')->nullable();
		});
	}

	public function down()
	{
		Schema::dropIfExists('players');
	}
}
