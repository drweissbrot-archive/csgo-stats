<?php

namespace App\Http\Controllers;

use App\Map;
use App\Player;
use App\Support\Concerns\FindsTeamOfPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use SteamID;

class PlayerController extends Controller
{
	use FindsTeamOfPlayer;

	public function __invoke(Request $request, Player $player)
	{
		$player->load([
			'teams.series' => function ($series) use ($player, $request) {
				$series->with([
					'teams.players' => function ($players) use ($player) {
						$players->where('players.id', $player->id);
					},

					'matches' => function ($matches) use ($player) {
						$matches->with([
							'playerMatchStats' => function ($playerMatchStats) use ($player) {
								$playerMatchStats->where('player_id', $player->id);
							},

							'rounds' => function ($rounds) {
								$rounds->onlyCounted();
							},
						])->withoutKnifeRounds();
					},
				]);

				if ($request->alltime !== '1') {
					$series->whereHas('matches', function ($matches) {
						$matches->where('started_at', '>=', today()->subDays(90));
					});
				}
			},
		]);

		$series = $player->teams->pluck('series')->flatten(1);

		return view('player.read', compact('player', 'series'), [
			'steamId' => $player->bot ? null : new SteamID($player->steam_id),
			'stats' => $this->buildStats($series, $player),
			'matches' => $series->pluck('matches')->flatten(1),

			'maps' => Map::orderBy('map_group')->orderBy('display_name')->get(),
		]);
	}

	protected function buildStats(Collection $allSeries, Player $player) : Collection
	{
		$stats = collect();

		foreach ($allSeries as $series) {
			$letter = $this->teamOf($player, $series->teamA, $series->teamB);
			$team = $series->teams->firstWhere('pivot.letter', $letter);

			if (! $stats->has($series->ladder_id)) {
				$stats->put($series->ladder_id, collect());
			}

			foreach ($series->matches as $match) {
				if (! $stats->get($series->ladder_id)->has($match->map_id)) {
					$stats->get($series->ladder_id)->put($match->map_id, collect([
						'ct_regulation' => collect(), 't_regulation' => collect(),
						'ct_pistol' => collect(), 't_pistol' => collect(),
						'ct_overtime' => collect(), 't_overtime' => collect(),
					]));
				}

				$ownScore = $match->{"team_{$letter}_score"};
				$otherScore = $match->{'team_' . otherTeam($letter) . '_score'};

				$stats->get($series->ladder_id)->get($match->map_id)->each(function ($phase) use ($ownScore, $otherScore) {
					$phase->addNum('matches_played', 1)
						->addNum('matches_won', ($ownScore > $otherScore) ? 1 : 0)
						->addNum('matches_tied', ($ownScore === $otherScore) ? 1 : 0)
						->addNum('matches_lost', ($ownScore < $otherScore) ? 1 : 0)
						->addNum('round_difference', $ownScore - $otherScore);
				});

				foreach ($match->rounds as $round) {
					$side = teamNumberToAbbr($round->{"team_{$letter}_side"});
					$phase = ($round->round_no > 29) ? 'overtime' : 'regulation';

					$stats->get($series->ladder_id)->get($match->map_id)->get("{$side}_{$phase}")->addNum('rounds_played', 1);

					if ($round->winner_team_id === $team->id) {
						$stats->get($series->ladder_id)->get($match->map_id)->get("{$side}_{$phase}")->addNum('rounds_won', 1);
					}

					if ($round->round_no === 0 || $round->round_no === 15) {
						$stats->get($series->ladder_id)->get($match->map_id)->get("{$side}_pistol")->addNum('rounds_played', 1);

						if ($round->winner_team_id === $team->id) {
							$stats->get($series->ladder_id)->get($match->map_id)->get("{$side}_pistol")->addNum('rounds_won', 1);
						}
					}
				}

				foreach ($match->playerMatchStats as $stat) {
					foreach ($stat->getAttributes() as $key => $value) {
						if (is_numeric($value)) {
							$stats->get($series->ladder_id)->get($match->map_id)->get("{$stat->side}_{$stat->phase}")->addNum($key, $value);
						}
					}
				}
			}
		}

		return $stats;
	}
}
