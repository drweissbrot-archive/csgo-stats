<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BotTakeoverEvent extends Model
{
	protected $guarded = [];

	protected $with = ['human', 'bot'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'human_id' => $players->get($data->get('human')),
			'bot_id' => $players->get($data->get('bot')),
		]);
	}

	public function human()
	{
		return $this->belongsTo(Player::class);
	}

	public function bot()
	{
		return $this->belongsTo(Player::class);
	}
}
