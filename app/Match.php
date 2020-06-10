<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Match extends Model
{
	protected $guarded = [];

	protected $dates = ['started_at'];

	public function scopeWithoutKnifeRounds(Builder $matches)
	{
		return $matches->where('is_knife_round', false);
	}

	public function series()
	{
		return $this->belongsTo(Series::class);
	}

	public function rounds()
	{
		return $this->hasMany(Round::class)
			->orderBy('round_no', 'ASC');
	}

	public function winnerTeam()
	{
		return $this->belongsTo(Team::class);
	}

	public function map()
	{
		return $this->belongsTo(Map::class);
	}

	public function playerMatchStats()
	{
		return $this->hasMany(PlayerMatchStat::class);
	}

	// cf. https://github.com/saul/demofile/issues/146#issuecomment-615862422
	public function playersPerTeam() : int
	{
		if ($this->game_type === 0) {
			if ($this->game_mode === 1 || $this->game_mode === 3) {
				return 5;
			}

			if ($this->game_mode === 2) {
				return 2;
			}
		}
	}

	/**
	 * @param int $roundNo round number, zero-indexed
	 *
	 * @return bool if the sides were swapped at the beginning of the given round
	 */
	public function isSwapSideRound(int $roundNo) : bool
	{
		if (! $this->has_halftime) {
			return false;
		}

		return $roundNo === ($this->max_rounds / 2) || ($roundNo > $this->max_rounds && ($roundNo - 3) % 6 === 0);
	}
}
