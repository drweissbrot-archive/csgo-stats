<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapsTable extends Migration
{
	public function up()
	{
		Schema::create('maps', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->string('display_name');
			$table->string('filename');
			$table->string('name');
			$table->string('map_group');
		});
	}

	public function down()
	{
		Schema::dropIfExists('maps');
	}
}
