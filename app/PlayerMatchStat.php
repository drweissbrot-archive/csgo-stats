<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class PlayerMatchStat extends Model
{
	protected $guarded = [];

	public function player()
	{
		return $this->belongsTo(Player::class);
	}
}
