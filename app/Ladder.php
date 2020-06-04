<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Ladder extends Model
{
	protected $guarded = [];

	public function series()
	{
		return $this->hasMany(Series::class);
	}
}
