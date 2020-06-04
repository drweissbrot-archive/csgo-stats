<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
	public function up()
	{
		Schema::create('teams', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->string('name');
			$table->string('flag')->nullable();
		});
	}

	public function down()
	{
		Schema::dropIfExists('teams');
	}
}
