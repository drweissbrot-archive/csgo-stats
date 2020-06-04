<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Series extends Model
{
	protected $guarded = [];

	public function ladder()
	{
		return $this->belongsTo(Ladder::class);
	}

	public function teams()
	{
		return $this->belongsToMany(Team::class)->withPivot('letter');
	}

	public function matches()
	{
		return $this->hasMany(Match::class)
			->orderBy('index_within_series');
	}

	public function hasKnifeRound() : bool
	{
		if ($this->relationLoaded('matches')) {
			return (bool) $this->matches->firstWhere('is_knife_round', true);
		}

		return $this->matches()->where('is_knife_round', true)->exists();
	}

	public function getTeamAAttribute()
	{
		return $this->teams->firstWhere('pivot.letter', 'a');
	}

	public function getTeamBAttribute()
	{
		return $this->teams->firstWhere('pivot.letter', 'b');
	}
}
