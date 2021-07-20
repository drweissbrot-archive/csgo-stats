<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAliasToSeries extends Migration
{
	public function up()
	{
		Schema::table('series', function (Blueprint $table) {
			$table->string('alias')->nullable()->unique();
		});
	}

	public function down()
	{
		Schema::table('series', function (Blueprint $table) {
			$table->dropColumn('alias');
		});
	}
}
