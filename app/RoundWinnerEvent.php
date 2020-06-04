<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RoundWinnerEvent extends Model
{
	protected $guarded = [];

	protected $with = ['winnerTeam'];

	public static function makeFromData(Collection $data, Collection $demo, Collection $players, int $i, $winnerTeamId) : self
	{
		return static::make([
			'index_within_round' => $i,
			'tick' => $data->get('tick'),

			'winner_team_id' => $winnerTeamId,

			'winner_side' => $data->get('winner'),
			'reason' => $data->get('reason'),
		]);
	}

	public function winnerTeam()
	{
		return $this->belongsTo(Team::class);
	}
}
