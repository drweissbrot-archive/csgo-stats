<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeetifyMatchIdToMatches extends Migration
{
	public function up()
	{
		Schema::table('matches', function (Blueprint $table) {
			$table->string('leetify_match_id')->nullable();
		});
	}

	public function down()
	{
		Schema::table('matches', function (Blueprint $table) {
			$table->dropColumn('leetify_match_id');
		});
	}
}
