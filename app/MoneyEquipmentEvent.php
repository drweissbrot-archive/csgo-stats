<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MoneyEquipmentEvent extends Model
{
	protected $with = ['player'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'money_remaining' => $data->get('money_remaining'),
			'equipment_value' => $data->get('equipment_value'),

			'player_id' => $players->get($data->get('player')) ?? unknownUser()->id,
		]);
	}

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
