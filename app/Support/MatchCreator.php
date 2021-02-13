<?php

namespace App\Support;

use App\CsMatch;
use App\Map;
use App\Series;
use App\Support\Concerns\FindsAndCreatesPlayers;
use App\Team;
use DB;
use Exception;
use Facades\App\Support\ScoreBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MatchCreator
{
	use FindsAndCreatesPlayers;

	public function fromDemo(
		Collection $demo,
		Series $series,
		int $indexWithinSeries,
		bool $isKnife,
		Carbon $startedAt,
		string $demoPath = null,
		string $notes = null
	) : CsMatch {
		return DB::transaction(function () use ($demo, $series, $indexWithinSeries, $isKnife, $startedAt, $demoPath, $notes) {
			$series->loadMissing('teams.players');

			$players = $this->findOrCreatePlayers($demo);
			[$teamA, $sideA, $teamB, $sideB] = $this->setUpTeams($series, $demo, $players);

			$match = $series->matches()->make([
				'server_name' => $demo->get('meta')->get('server_name'),
				'tickrate' => $demo->get('meta')->get('tickrate'),
				'ticks' => $demo->get('meta')->get('ticks'),
				'duration' => $demo->get('meta')->get('duration'),

				'index_within_series' => $indexWithinSeries,
				'is_knife_round' => $isKnife,

				'round_count' => $demo->get('rounds')->last()->first(fn ($e) => in_array($e->get('type'), ['round_start', 'freeze_time_ended']))->get('number') + 1,
				'max_rounds' => $demo->get('meta')->get('max_rounds'),
				'has_halftime' => $demo->get('meta')->get('has_halftime'),

				'team_a_score' => $demo->get('teams')->get($sideA)->get('score'),
				'team_a_score_first_half' => $demo->get('teams')->get($sideA)->get('score_first_half'),
				'team_a_score_second_half' => $demo->get('teams')->get($sideA)->get('score_second_half'),
				'team_a_score_ot' => $demo->get('teams')->get($sideA)->get('score_overtime'),

				'team_b_score' => $demo->get('teams')->get($sideB)->get('score'),
				'team_b_score_first_half' => $demo->get('teams')->get($sideB)->get('score_first_half'),
				'team_b_score_second_half' => $demo->get('teams')->get($sideB)->get('score_second_half'),
				'team_b_score_ot' => $demo->get('teams')->get($sideB)->get('score_overtime'),

				'started_at' => $startedAt,
				'demo_path' => $demoPath,
				'notes' => $notes,

				'game_mode' => (int) $demo->get('meta')->get('game_mode'),
				'game_type' => (int) $demo->get('meta')->get('game_type'),
			]);

			$match->team_a_started_on = $this->findTeamAStartingSide($match, $sideA);
			$match->team_b_started_on = otherTeam($match->team_a_started_on);

			if ($match->team_a_score > $match->team_b_score) {
				$match->winner_team_id = $teamA->id;
			} elseif ($match->team_b_score > $match->team_a_score) {
				$match->winner_team_id = $teamB->id;
			} // otherwise, it's probably a tie

			$match->map_id = optional(Map::whereName($demo->get('meta')->get('map'))->first())->id;
			throw_unless($match->map_id, new Exception("map {$demo->get('meta')->get('map')} does not exist"));

			$match->save();

			$players = $players->pluck('id', 'steam_id');

			$sideA = teamAbbrToNumber($match->team_a_started_on);
			$sideB = teamAbbrToNumber($match->team_b_started_on);

			foreach ($demo->get('rounds') as $events) {
				$roundNo = $events->first(fn ($e) => in_array($e->get('type'), ['round_start', 'freeze_time_ended']))->get('number');

				if ($match->isSwapSideRound($roundNo)) {
					$swap = $sideB;
					$sideB = $sideA;
					$sideA = $swap;
				}

				$winEvent = optional($events->firstWhere('type', 'round_winner'));

				if ($sideA === $winEvent->get('winner')) {
					$winnerTeamId = $teamA->id;
				} elseif ($sideB === $winEvent->get('winner')) {
					$winnerTeamId = $teamB->id;
				} // otherwise, it's probably a tie

				$round = $match->rounds()->create([
					'round_no' => $roundNo,
					'is_counted' => in_array($winEvent->get('winner'), [2, 3]),

					'win_side' => $winEvent->get('winner') ?? -1,
					'win_reason' => $winEvent->get('reason') ?? -1,

					'team_a_survived' => $match->playersPerTeam(),
					'team_b_survived' => $match->playersPerTeam(),

					'team_a_side' => $sideA,
					'team_b_side' => $sideB,

					'winner_team_id' => $winnerTeamId ?? null,
					'mvp_id' => $players->get(optional($events->firstWhere('type', 'mvp'))->get('mvp')),
				]);

				foreach ($events as $i => $event) {
					$round->addEvent($event, $demo, $players, $i, $winnerTeamId ?? null);
				}
			}

			ScoreBuilder::buildScores($match);

			return $match;
		});
	}

	protected function setUpTeams(Series $series, Collection $demo, Collection $players) : array
	{
		$teamA = $series->teamA->players->pluck('steam_id');
		$teamB = $series->teamB->players->pluck('steam_id');

		$t = $demo->get('teams')->get('t');
		$ct = $demo->get('teams')->get('ct');

		// these are technically "inverse" probabilities, i.e. lower means more likely to be that team
		$probabilities = collect([
			't_a' => $teamA->diff($t->get('players'))->count(),
			'ct_a' => $teamA->diff($ct->get('players'))->count(),
			't_b' => $teamB->diff($t->get('players'))->count(),
			'ct_b' => $teamB->diff($ct->get('players'))->count(),
		])->sort();

		$ctIs = $tIs = false;

		foreach ($probabilities as $association => $probability) {
			[$side, $team] = explode('_', $association);

			if (! ${"{$side}Is"}) {
				${"{$side}Is"} = $team;
			}
		}

		$sideA = ($ctIs === 'a') ? 'ct' : 't';
		$sideB = ($ctIs === 'b') ? 'ct' : 't';

		$series->teamA->players()->attach(
			$players->only($demo->get('teams')->get($sideA)->get('players')->diff($teamA))->pluck('id')
		);

		$series->teamB->players()->attach(
			$players->only($demo->get('teams')->get($sideB)->get('players')->diff($teamB))->pluck('id')
		);

		$series->load('teams.players');

		return [
			$series->teamA, $sideA,
			$series->teamB, $sideB,
		];
	}

	protected function findTeamAStartingSide(CsMatch $match, string $sideA) : string
	{
		if (! $match->has_halftime) {
			return $sideA;
		}

		$rounds = $match->team_a_score + $match->team_b_score;
		$swap = false;

		for ($i = 0; $i < $rounds; $i++) {
			if ($match->isSwapSideRound($i)) {
				$swap = ! $swap;
			}
		}

		return ($swap)
			? otherTeam($sideA)
			: $sideA;
	}
}
