<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FlashedEvent extends Model
{
	protected $guarded = [];

	protected $with = ['attacker', 'victim'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'attacker_id' => $players->get($data->get('attacker')) ?? unknownUser()->id,
			'victim_id' => $players->get($data->get('victim')) ?? unknownUser()->id,

			'flashbang_entity_id' => $data->get('entity_id'),
			'duration' => $data->get('duration'),

			'teamflash' => (bool) $demo->get('teams')->first(function ($team) use ($data) {
				return $team->get('players')->contains($data->get('attacker'))
					&& $team->get('players')->contains($data->get('victim'));
			}),
		]);
	}

	public function attacker()
	{
		return $this->belongsTo(Player::class);
	}

	public function victim()
	{
		return $this->belongsTo(Player::class);
	}
}
