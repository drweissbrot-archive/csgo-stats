<?php

use App\Map;
use Illuminate\Database\Seeder;

class MapsSeeder extends Seeder
{
	public function run()
	{
		Map::create([
			'display_name' => 'de_austria',
			'filename' => 'de_austria',
			'name' => 'workshop/727462766/de_austria',
			'map_group' => 'workshop',
		]);

		Map::create([
			'display_name' => 'de_cache',
			'filename' => 'de_cache',
			'name' => 'de_cache',
			'map_group' => 'reserves',
		]);

		Map::create([
			'display_name' => 'de_cbble [Compatibility Version 1.36.5.6]',
			'filename' => 'de_cbble',
			'name' => 'workshop/1542127528/de_cbble',
			'map_group' => 'workshop',
		]);

		Map::create([
			'display_name' => 'de_dust2',
			'filename' => 'de_dust2',
			'name' => 'de_dust2',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_inferno',
			'filename' => 'de_inferno',
			'name' => 'de_inferno',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_mirage',
			'filename' => 'de_mirage',
			'name' => 'de_mirage',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_nuke',
			'filename' => 'de_nuke',
			'name' => 'de_nuke',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_overpass',
			'filename' => 'de_overpass',
			'name' => 'de_overpass',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_santorini',
			'filename' => 'de_santorini',
			'name' => 'workshop/546623875/de_santorini',
			'map_group' => 'workshop',
		]);

		Map::create([
			'display_name' => 'de_train',
			'filename' => 'de_train',
			'name' => 'de_train',
			'map_group' => 'active_duty',
		]);

		Map::create([
			'display_name' => 'de_vertigo',
			'filename' => 'de_vertigo',
			'name' => 'de_vertigo',
			'map_group' => 'active_duty',
		]);
	}
}
