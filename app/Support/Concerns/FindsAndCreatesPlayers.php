<?php

namespace App\Support\Concerns;

use App\Player;
use Illuminate\Support\Collection;

trait FindsAndCreatesPlayers
{
	protected function findOrCreatePlayers(Collection $demo) : Collection
	{
		$players = Player::whereIn(
			'steam_id',
			$demo->get('playerMeta')->keys()->merge(['unknown_user']),
		)->get()->keyBy('steam_id');

		foreach ($demo->get('playerMeta') as $steamId => $player) {
			if (! $players->has($steamId)
				&& (
					$demo->get('teams')->get('t')->get('players')->contains($steamId)
					|| $demo->get('teams')->get('ct')->get('players')->contains($steamId)
				)
			) {
				$players->put($steamId, Player::create([
					'steam_id' => $steamId,
					'display_name' => $player->get('name'),
					'steam_name' => $player->get('name'),
					'bot' => $player->get('bot'),
				]));
			}
		}

		// convert to a normal (i.e. non-eloquent) collection since we will call ->only() later
		return collect($players);
	}
}
