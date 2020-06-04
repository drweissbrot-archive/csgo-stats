<?php

use App\Player;
use Illuminate\Database\Seeder;

class UnknownUserSeeder extends Seeder
{
	public function run()
	{
		Player::create([
			'display_name' => 'unknown user',
			'steam_name' => 'unknown user',
			'steam_id' => 'unknown_user',
		]);
	}
}
