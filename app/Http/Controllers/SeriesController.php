<?php

namespace App\Http\Controllers;

use App\Series;

class SeriesController extends Controller
{
	public function read(Series $series)
	{
		$series->load([
			'teams.players', 'ladder',
			'matches.map', 'matches.playerMatchStats',

			'matches.rounds' => function ($rounds) {
				$rounds->onlyCounted();
			},
		]);

		if ($series->matches->count() < 2) {
			return redirect()->route('match', $series->matches->first()->id);
		}

		$matchesExceptKnifeRounds = $series->matches->where('is_knife_round', false);

		$stats = $matchesExceptKnifeRounds->pluck('playerMatchStats')->flatten(1)
			->groupBy('player_id')->map->mapToGroups(function ($stat) {
				return ["{$stat->side}_{$stat->phase}" => collect($stat->getAttributes())->filter(function ($value) {
					return is_numeric($value);
				})];
			})->map(function ($phases) {
				return $phases->map(function ($matches) {
					return $matches->reduce(function ($sum, $match) {
						if ($sum === null) {
							return $match;
						}

						foreach ($match as $key => $value) {
							$sum->addNum($key, $value);
						}

						return $sum;
					});
				});
			});

		$roundsPlayed = $roundsWon = [
			'a' => [
				'regulation' => ['ct' => 0, 't' => 0],
				'pistol' => ['ct' => 0, 't' => 0],
				'overtime' => ['ct' => 0, 't' => 0],
			],
			'b' => [
				'regulation' => ['ct' => 0, 't' => 0],
				'pistol' => ['ct' => 0, 't' => 0],
				'overtime' => ['ct' => 0, 't' => 0],
			],
		];

		foreach ($series->matches->where('is_knife_round', false)->pluck('rounds')->flatten(1) as $round) {
			$phase = ($round->round_no < 30) ? 'regulation' : 'overtime';

			$roundsPlayed['a'][$phase][teamNumberToAbbr($round->team_a_side)]++;
			$roundsPlayed['b'][$phase][teamNumberToAbbr($round->team_b_side)]++;

			if ($round->round_no === 0 || $round->round_no === 15) {
				$roundsPlayed['a']['pistol'][teamNumberToAbbr($round->team_a_side)]++;
				$roundsPlayed['b']['pistol'][teamNumberToAbbr($round->team_b_side)]++;
			}

			if ($round->winner_team_id === $series->teamA->id) {
				$roundsWon['a'][$phase][teamNumberToAbbr($round->team_a_side)]++;
			} elseif ($round->winner_team_id === $series->teamB->id) {
				$roundsWon['b'][$phase][teamNumberToAbbr($round->team_b_side)]++;
			}
		}

		$teams = collect(['a' => $series->teamA, 'b' => $series->teamB])->map(function ($team, $letter) use ($matchesExceptKnifeRounds, $roundsPlayed, $roundsWon, $stats) {
			return collect([
				'score' => $matchesExceptKnifeRounds->where('winner_team_id', $team->id)->count(),
				'score_first_half' => divide($roundsWon[$letter]['regulation']['ct'], $roundsPlayed[$letter]['regulation']['ct']),
				'score_second_half' => divide($roundsWon[$letter]['regulation']['t'], $roundsPlayed[$letter]['regulation']['t']),
				'score_overtime' => divide($roundsWon[$letter]['overtime']['ct'], $roundsPlayed[$letter]['overtime']['ct']),
				'score_overtime_2' => divide($roundsWon[$letter]['overtime']['t'], $roundsPlayed[$letter]['overtime']['t']),

				'side_first_half' => 'ct',
				'side_second_half' => 't',

				'name' => $team->name,
				'flag' => $team->flag,
				'players' => $team->players->map(function ($player) use ($stats) {
					return array_merge($player->only('id', 'display_name', 'steam_name', 'flag', 'avatar_url', 'bot'), [
						'stats' => $stats->get($player->id),
					]);
				}),
			]);
		});

		$teams->transform(function ($team, $letter) use ($teams) {
			$otherTeamScore = $teams->get($letter === 'a' ? 'b' : 'a')->get('score');

			return $team->put('winner', $team->get('score') > $otherTeamScore)
				->put('loser', $otherTeamScore > $team->get('score'));
		});

		return view('series.read', compact('matchesExceptKnifeRounds', 'roundsPlayed', 'series', 'teams'));
	}
}
