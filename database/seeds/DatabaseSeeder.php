<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public function run()
	{
		$this->call(MapsSeeder::class);
		$this->call(UnknownUserSeeder::class);
	}
}
