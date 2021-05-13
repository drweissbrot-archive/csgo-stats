<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnemyKillsSmokegrenadeToPlayerMatchStats extends Migration
{
	public function up()
	{
		Schema::table('player_match_stats', function (Blueprint $table) {
			$table->integer('enemy_kills_smokegrenade')->default(0);
		});
	}

	public function down()
	{
		Schema::table('player_match_stats', function (Blueprint $table) {
			$table->dropColumn('enemy_kills_smokegrenade');
		});
	}
}
