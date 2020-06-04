<?php

namespace App\Support\Concerns;

use App\Player;
use App\Team;
use Str;

trait FindsTeamOfPlayer
{
	protected function teamOf(Player $player, Team $teamA, Team $teamB) : string
	{
		if ($teamA->players->contains($player)) {
			return 'a';
		}

		if ($teamB->players->contains($player)) {
			return 'b';
		}

		if (Str::startsWith($player->steam_id, 'BOT_') || $player->steam_id === 'unknown_user') {
			return 'z';
		}
	}
}
