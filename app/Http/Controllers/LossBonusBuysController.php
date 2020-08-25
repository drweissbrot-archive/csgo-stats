<?php

namespace App\Http\Controllers;

class LossBonusBuysController extends Controller
{
	protected const PRICES = [
		'kevlar' => 650,
		'helmet' => 350,
		'defuser' => 400,

		'm4a1_silencer' => 2900,
		'ak47' => 2700,
		'awp' => 4750,

		'incgrenade' => 600,
		'molotov' => 400,
		'smokegrenade' => 300,
		'flashbang' => 200,
	];

	protected const LOADOUTS = [
		'ct_full' => ['kevlar', 'defuser', 'm4a1_silencer', 'incgrenade', 'smokegrenade', 'flashbang', 'flashbang'],
		'ct_desperate' => ['kevlar', 'm4a1_silencer'],

		't_full' => ['kevlar', 'helmet', 'ak47', 'molotov', 'smokegrenade', 'flashbang', 'flashbang'],
		't_desperate' => ['kevlar', 'ak47'],

		'awp_full' => ['kevlar', 'defuser', 'awp', 'molotov', 'smokegrenade', 'flashbang', 'flashbang'],
		'awp_desperate' => ['kevlar', 'awp'],
	];

	public function __invoke()
	{
		return view('loss-bonus-buys.index', [
			'loadouts' => static::LOADOUTS,
			'prices' => $this->calculateLoadoutPrices(),
		]);
	}

	protected function calculateLoadoutPrices() : array
	{
		$prices = [];

		foreach (static::LOADOUTS as $loadout => $items) {
			$price = 0;

			foreach ($items as $item) {
				$price += static::PRICES[$item];
			}

			$prices[$loadout] = $price;
		}

		return $prices;
	}
}
