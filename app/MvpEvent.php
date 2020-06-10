<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MvpEvent extends Model
{
	protected $guarded = [];

	protected $with = ['mvp'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'mvp_id' => $players->get($data->get('mvp')) ?? unknownUser()->id,
		]);
	}

	public function mvp()
	{
		return $this->belongsTo(Player::class);
	}
}
