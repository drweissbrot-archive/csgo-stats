<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBotIdNullableOnBotTakeoverEvents extends Migration
{
	public function up()
	{
		Schema::table('bot_takeover_events', function (Blueprint $table) {
			$table->uuid('bot_id')->nullable()->change();
		});
	}

	public function down()
	{
		Schema::table('bot_takeover_events', function (Blueprint $table) {
			$table->uuid('bot_id')->nullable(false)->change();
		});
	}
}
