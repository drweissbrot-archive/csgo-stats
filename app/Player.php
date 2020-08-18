<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Player extends Model
{
	protected $guarded = [];

	protected $casts = [
		'bot' => 'bool',
	];

	public function teams()
	{
		return $this->belongsToMany(Team::class);
	}
}
