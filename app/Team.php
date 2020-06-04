<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Team extends Model
{
	protected $guarded = [];

	public function players()
	{
		return $this->belongsToMany(Player::class);
	}

	public function series()
	{
		return $this->belongsToMany(Series::class);
	}
}
