<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ItemPickupEvent extends Model
{
	protected $guarded = [];

	protected $with = ['player'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'player_id' => $players->get($data->get('player')) ?? unknownUser()->id,
			'item' => $data->get('item'),
		]);
	}

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
