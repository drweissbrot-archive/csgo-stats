<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class KillEvent extends Model
{
	protected $guarded = [];

	protected $with = ['attacker', 'victim', 'assister'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'attacker_id' => $players->get($data->get('attacker')) ?? unknownUser()->id,
			'victim_id' => $players->get($data->get('victim')) ?? unknownUser()->id,

			'assister_id' => $data->get('assister')
				? ($players->get($data->get('assister')) ?? unknownUser()->id)
				: null,
			'flash_assist' => (bool) $data->get('flash_assist'),

			'weapon' => $data->get('weapon'),
			'headshot' => (bool) $data->get('headshot'),
			'through_wall' => (bool) $data->get('through_wall'),
			'noscope' => (bool) $data->get('noscope'),
			'through_smoke' => (bool) $data->get('through_smoke'),
			'attacker_flashed' => (bool) $data->get('attacker_flashed'),

			'teamkill' => (bool) $demo->get('teams')->first(function ($team) use ($data) {
				return $team->get('players')->contains($data->get('attacker'))
					&& $team->get('players')->contains($data->get('victim'));
			}),

			'team_assist' => $data->get('assister') && (bool) $demo->get('teams')->first(function ($team) use ($data) {
				return $team->get('players')->contains($data->get('assister'))
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

	public function assister()
	{
		return $this->belongsTo(Player::class);
	}
}
