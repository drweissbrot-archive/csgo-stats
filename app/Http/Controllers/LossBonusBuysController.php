<?php

namespace App\Http\Controllers;

class LossBonusBuysController extends Controller
{
	protected const PRICES = [
		'kevlar' => 650,
		'helmet' => 350,
		'defuser' => 400,

		'm4a1_silencer' => 2900,
		'm4a1' => 3100,
		'ak47' => 2700,
		'awp' => 4750,

		'incgrenade' => 600,
		'molotov' => 400,
		'smokegrenade' => 300,
		'flashbang' => 200,
		'hegrenade' => 300,
	];

	protected const LOADOUTS = [
		'ct_full' => ['kevlar', 'defuser', 'm4a1_silencer', 'incgrenade', 'smokegrenade', 'flashbang', 'hegrenade'],
		'ct_desperate' => ['kevlar', 'm4a1_silencer'],

		't_full' => ['kevlar', 'helmet', 'ak47', 'molotov', 'smokegrenade', 'flashbang', 'hegrenade'],
		't_desperate' => ['kevlar', 'ak47'],

		'awp_full' => ['kevlar', 'defuser', 'awp', 'molotov', 'smokegrenade', 'flashbang', 'hegrenade'],
		'awp_desperate' => ['kevlar', 'awp'],
	];

	public function m4a1()
	{
		return $this->view('M4A1', static::LOADOUTS);
	}

	public function m4a4()
	{
		return $this->view('M4A4', $this->replaceLoadouts('m4a1_silencer', 'm4a1'));
	}

	public function wingman_m4a1()
	{
		return $this->view('Wingman M4A1', static::LOADOUTS, 2000, 300);
	}

	public function wingman_m4a4()
	{
		return $this->view('Wingman M4A4', $this->replaceLoadouts('m4a1_silencer', 'm4a1'), 2000, 300);
	}

	protected function view(string $title, $loadouts, $initialBonus = 1400, $bonusIncrement = 500)
	{
		return view('loss-bonus-buys.index', [
			'prices' => $this->calculateLoadoutPrices($loadouts),
		], compact('title', 'loadouts', 'initialBonus', 'bonusIncrement'));
	}

	protected function replaceLoadouts(string $needle, string $replacement) : array
	{
		$loadouts = static::LOADOUTS;

		foreach ($loadouts as $loadout => $weapons) {
			foreach ($weapons as $i => $weapon) {
				if ($weapon === $needle) {
					$loadouts[$loadout][$i] = $replacement;
				}
			}
		}

		return $loadouts;
	}

	protected function calculateLoadoutPrices(array $loadouts) : array
	{
		$prices = [];

		foreach ($loadouts as $loadout => $items) {
			$price = 0;

			foreach ($items as $item) {
				$price += static::PRICES[$item];
			}

			$prices[$loadout] = $price;
		}

		return $prices;
	}
}
