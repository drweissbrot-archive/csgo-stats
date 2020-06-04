<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesTable extends Migration
{
	public function up()
	{
		Schema::create('series', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->string('name')->nullable();
			$table->integer('best_of')->unsigned();
			$table->text('notes')->nullable();

			$table->uuid('ladder_id')->index();
			$table->foreign('ladder_id')->references('id')->on('ladders')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('series');
	}
}
