<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FlashbangDetonatedEvent extends Model
{
	protected $with = ['thrower'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'flashbang_entity_id' => $data->get('entity_id'),

			'thrower_id' => $players->get($data->get('thrower')),
		]);
	}

	public function thrower()
	{
		return $this->belongsTo(Player::class);
	}
}
