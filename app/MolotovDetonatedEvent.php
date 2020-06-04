<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

// NB! Currently (2020-05-29), this event is not fired in demos, and thus shouldn't be used. Use MolotovThrownEvent instead. This is kept in case demos start using this event again.
class MolotovDetonatedEvent extends Model
{
	protected $with = ['thrower'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'thrower_id' => $players->get($data->get('thrower')),
		]);
	}

	public function thrower()
	{
		return $this->belongsTo(Player::class);
	}
}
