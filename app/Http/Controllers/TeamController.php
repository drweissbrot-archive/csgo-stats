<?php

namespace App\Http\Controllers;

use App\Map;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TeamController extends Controller
{
	public function __invoke(Request $request, Team $team)
	{
		$team->load('players');

		$team->load([
			'series' => function ($series) use ($request, $team) {
				$series->with([
					'teams',

					'matches' => function ($matches) use ($team) {
						$matches->with([
							'playerMatchStats' => function ($stats) use ($team) {
								$stats->whereIn('player_id', $team->players->pluck('id'));
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

		return view('team.read', compact('team'), [
			'stats' => $this->buildStats($team->series, $team),
			'matches' => $team->series->pluck('matches')->flatten(1),

			'maps' => Map::orderBy('map_group')->orderBy('display_name')->get(),
		]);
	}

	protected function buildStats(Collection $allSeries, Team $team) : Collection
	{
		$stats = $team->players->mapWithKeys(fn ($player) => [
			$player->id => app('all_ladders')->mapWithKeys(fn ($ladder) => [$ladder->id => collect()]),
		]);

		foreach ($allSeries as $series) {
			$letter = $this->teamLetter($team, $series->teamA, $series->teamB);

			foreach ($series->matches as $match) {
				$ownScore = $match->{"team_{$letter}_score"};
				$otherScore = $match->{'team_' . otherTeam($letter) . '_score'};

				foreach ($stats as $player) {
					if (! $player->get($series->ladder_id)->has($match->map_id)) {
						$player->get($series->ladder_id)->put($match->map_id, collect([
							'ct_regulation' => collect(), 't_regulation' => collect(),
							'ct_pistol' => collect(), 't_pistol' => collect(),
							'ct_overtime' => collect(), 't_overtime' => collect(),
						]));
					}

					$player->get($series->ladder_id)->get($match->map_id)->each(function ($phase) use ($ownScore, $otherScore) {
						$phase->addNum('matches_played', 1)
							->addNum('matches_won', ($ownScore > $otherScore) ? 1 : 0)
							->addNum('matches_tied', ($ownScore === $otherScore) ? 1 : 0)
							->addNum('matches_lost', ($ownScore < $otherScore) ? 1 : 0)
							->addNum('round_difference', $ownScore - $otherScore);
					});
				}

				foreach ($match->rounds as $round) {
					$side = teamNumberToAbbr($round->{"team_{$letter}_side"});
					$phase = ($round->round_no > 29) ? 'overtime' : 'regulation';

					foreach ($stats as $player) {
						$player->get($series->ladder_id)->get($match->map_id)->get("{$side}_{$phase}")->addNum('rounds_played', 1);

						if ($round->winner_team_id === $team->id) {
							$player->get($series->ladder_id)->get($match->map_id)->get("{$side}_{$phase}")->addNum('rounds_won', 1);
						}

						if ($round->round_no === 0 || $round->round_no === 15) {
							$player->get($series->ladder_id)->get($match->map_id)->get("{$side}_pistol")->addNum('rounds_played', 1);

							if ($round->winner_team_id === $team->id) {
								$player->get($series->ladder_id)->get($match->map_id)->get("{$side}_pistol")->addNum('rounds_won', 1);
							}
						}
					}
				}

				foreach ($match->playerMatchStats as $stat) {
					foreach ($stat->getAttributes() as $key => $value) {
						if (is_numeric($value)) {
							$stats->get($stat->player_id)->get($series->ladder_id)->get($match->map_id)->get("{$stat->side}_{$stat->phase}")->addNum($key, $value);
						}
					}
				}
			}
		}

		return $stats;
	}

	protected function teamLetter(Team $team, Team $a, Team $b) : string
	{
		if ($team->id === $a->id) {
			return 'a';
		}

		if ($team->id === $b->id) {
			return 'b';
		}
	}
}
