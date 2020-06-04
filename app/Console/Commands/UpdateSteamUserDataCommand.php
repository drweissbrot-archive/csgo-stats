<?php

namespace App\Console\Commands;

use App\Player;
use Illuminate\Console\Command;
use SteamID;

class UpdateSteamUserDataCommand extends Command
{
	protected $signature = 'steam:update-users';

	protected $description = 'Update Steam names, flags, and avatar URLs for all users';

	public function handle()
	{
		$players = Player::where('steam_id', '!=', 'unknown_user')->where('steam_id', 'NOT LIKE', 'BOT%')
			->get()->keyBy(function ($player) {
				return (new SteamID($player->steam_id))->convertToUInt64();
			});

		$steamData = collect();

		foreach ($players->chunk(100) as $chunk) {
			$url = 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=' . config('services.steam.key') . '&steamids=' . $chunk->keys()->join(',');

			$steamData = $steamData->merge(json_decode(file_get_contents($url))->response->players);
		}

		foreach ($steamData as $user) {
			$player = $players->get($user->steamid);
			$player->steam_name = $user->personaname;
			$player->flag = mb_strtolower($user->loccountrycode ?? '');
			$player->avatar_url = $user->avatarfull;
			$player->save();
		}
	}
}
