<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DefuseEvent extends Model
{
	protected $guarded = [];

	protected $with = ['defuser'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'defuser_id' => $players->get($data->get('defuser')) ?? unknownUser()->id,
			'site' => $data->get('site'),
		]);
	}

	public function defuser()
	{
		return $this->belongsTo(Player::class);
	}
}
