<?php

namespace App\Http\Controllers;

use App\BotTakeoverEvent;
use App\DamageEvent;
use App\DefuseEvent;
use App\FlashbangDetonatedEvent;
use App\FlashedEvent;
use App\FreezeTimeEndedEvent;
use App\ItemPickupEvent;
use App\KillEvent;
use App\Match;
use App\PlantEvent;
use App\Player;
use App\Round;
use App\Support\Concerns\FindsTeamOfPlayer;
use App\Team;
use Storage;

class MatchController extends Controller
{
	use FindsTeamOfPlayer;

	public function read(Match $match)
	{
		$match->load([
			'series.teams.players', 'series.ladder',
			'map', 'playerMatchStats',

			'rounds' => function ($rounds) {
				$rounds->where('is_counted', true);
			},
		]);

		$stats = $match->playerMatchStats->groupBy('player_id')->map->mapWithKeys(function ($stat) {
			return ["{$stat->side}_{$stat->phase}" => collect($stat->getAttributes())->filter(function ($value) {
				return is_numeric($value);
			})];
		});

		$teams = collect(['a' => $match->series->teamA, 'b' => $match->series->teamB])->map(function ($team, $letter) use ($match, $stats) {
			return collect([
				'score' => $match->{"team_{$letter}_score"},
				'score_first_half' => $match->{"team_{$letter}_score_first_half"},
				'score_second_half' => $match->{"team_{$letter}_score_second_half"},
				'score_overtime' => $match->{"team_{$letter}_score_ot"},

				'side_first_half' => $match->{"team_{$letter}_started_on"},
				'side_second_half' => otherTeam($match->{"team_{$letter}_started_on"}),

				'url' => route('team', $team->id),
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

		$roundsPlayed = [
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

		$rounds = $match->rounds->map(function ($round) use ($match, &$roundsPlayed) {
			if ($match->series->teamA->id === $round->winner_team_id) {
				$winner = 'a';
			} elseif ($match->series->teamB->id === $round->winner_team_id) {
				$winner = 'b';
			}

			$phase = ($round->round_no < 30) ? 'regulation' : 'overtime';

			$roundsPlayed['a'][$phase][teamNumberToAbbr($round->team_a_side)]++;
			$roundsPlayed['b'][$phase][teamNumberToAbbr($round->team_b_side)]++;

			if ($round->round_no === 0 || $round->round_no === 15) {
				$roundsPlayed['a']['pistol'][teamNumberToAbbr($round->team_a_side)]++;
				$roundsPlayed['b']['pistol'][teamNumberToAbbr($round->team_b_side)]++;
			}

			return [
				'winner' => $winner ?? null,
				'reason' => $round->win_reason,
				'side' => teamNumberToAbbr($round->win_side),
				'survived_a' => $round->team_a_survived,
				'survived_b' => $round->team_b_survived,
				'side_a' => $round->team_a_side,
				'side_b' => $round->team_b_side,
			];
		});

		return view('match.read', compact('match', 'teams', 'rounds', 'roundsPlayed'));
	}

	public function teamRoundPerformance(Match $match)
	{
		$match->load([
			'map', 'series.teams.players', 'series.ladder',

			'rounds' => function ($rounds) {
				$rounds->where('is_counted', true)->with(array_values(Round::EVENT_TYPES));
			},
		]);

		$rounds = $match->rounds->map(function ($round) use ($match) {
			return $this->roundToTeamPerformanceData($round, $match, $match->series->teamA, $match->series->teamB);
		});

		return view('match.team-round-performance', compact('match', 'rounds'));
	}

	public function playerRoundPerformance(Match $match, Player $player)
	{
		$match->load([
			'series.ladder', 'series.teams.players', 'map',

			'rounds' => function ($rounds) {
				$rounds->onlyCounted()->with(array_values(Round::EVENT_TYPES));
			},
		]);

		$letter = null;
		$team = null;

		if ($match->series->teamA->players->contains($player)) {
			$letter = 'a';
			$team = $match->series->teamA;
			$otherTeam = $match->series->teamB;
		} elseif ($match->series->teamB->players->contains($player)) {
			$letter = 'b';
			$team = $match->series->teamB;
			$otherTeam = $match->series->teamA;
		}

		abort_unless($team, 404);

		$rounds = $match->rounds->map(function ($round) use ($letter, $match, $otherTeam, $player, $team) {
			$events = collect();

			$firstTick = ($round->events->first(fn ($e) => $e instanceof FreezeTimeEndedEvent) ?? $round->events->first())->tick;

			$damage = 0;
			$realDamage = $otherTeam->players->mapWithKeys(fn ($player) => [$player->id => 0]);

			$flashbangs = [];

			foreach ($round->events as $event) {
				if ($event instanceof DamageEvent && ! $event->friendly_fire) {
					if ($player->id !== $event->attacker_id) {
						continue;
					}

					$damage += $event->damage;
					$realDamage->addNum($event->victim_id, $event->damage);
				} elseif ($event instanceof KillEvent) {
					if ($player->id === $event->attacker_id && ! $event->teamkill) {
						$events->push(collect([
							'time' => $this->time($event->tick, $firstTick, $match),
							'type' => 'kill',
							'weapon' => $event->weapon,
							'headshot' => $event->headshot,
							'attacker_flashed' => $event->attacker_flashed,
							'through_wall' => $event->through_wall,
							'through_smoke' => $event->through_smoke,
							'noscope' => $event->noscope,
							'victim' => $event->victim,
						]));
					} elseif ($player->id === $event->victim_id) {
						$events->push(collect([
							'time' => $this->time($event->tick, $firstTick, $match),
							'type' => 'death',
							'teamkill' => $event->teamkill,
							'weapon' => $event->weapon,
							'headshot' => $event->headshot,
							'attacker_flashed' => $event->attacker_flashed,
							'through_wall' => $event->through_wall,
							'through_smoke' => $event->through_smoke,
							'noscope' => $event->noscope,
							'attacker' => $event->attacker,
						]));
					} elseif ($player->id === $event->assister_id && ! $event->team_assist) {
						$events->push(collect([
							'time' => $this->time($event->tick, $firstTick, $match),
							'type' => 'assist',
							'flash_assist' => $event->flash_assist,
							'attacker' => $event->attacker,
							'victim' => $event->victim,
						]));
					}
				} elseif ($event instanceof PlantEvent && $player->id === $event->planter_id) {
					$events->push(collect([
						'time' => $this->time($event->tick, $firstTick, $match),
						'type' => 'plant',
						'site' => $event->site,
					]));
				} elseif ($event instanceof DefuseEvent && $player->id === $event->defuser_id) {
					$events->push(collect([
						'time' => $this->time($event->tick, $firstTick, $match),
						'type' => 'defuse',
						'site' => $event->site,
					]));
				} elseif ($event instanceof FlashbangDetonatedEvent) {
					if ($event->thrower_id !== $player->id) {
						continue;
					}

					$flashbangs[$event->flashbang_entity_id] = $events->count();

					$events->push(collect([
						'time' => $this->time($event->tick, $firstTick, $match),
						'type' => 'flashbang',
						'enemies_flashed' => 0,
						'enemies_flashed_duration' => 0,
						'teammates_flashed' => 0,
						'teammates_flashed_duration' => 0,
					]));
				} elseif ($event instanceof FlashedEvent) {
					if (! array_key_exists($event->flashbang_entity_id, $flashbangs)) {
						continue;
					}

					$events->get($flashbangs[$event->flashbang_entity_id])->addNum(
						($event->teamflash) ? 'teammates_flashed' : 'enemies_flashed', 1
					);

					$events->get($flashbangs[$event->flashbang_entity_id])->addNum(
						($event->teamflash) ? 'teammates_flashed_duration' : 'enemies_flashed_duration',
						$event->duration
					);
				} elseif ($event instanceof BotTakeoverEvent) {
					if ($event->human_id !== $player->id) {
						continue;
					}

					$events->push(collect([
						'time' => $this->time($event->tick, $firstTick, $match),
						'type' => 'bot_takeover',
						'bot' => $event->bot,
					]));
				} elseif ($event instanceof ItemPickupEvent) {
					if ($event->player_id !== $player->id) {
						continue;
					}

					$time = $this->time($event->tick, $firstTick, $match);

					if ($time < 4) {
						continue;
					}

					$events->push(collect([
						'time' => $time,
						'type' => 'item_pickup',
						'item' => $event->item,
					]));
				}
			}

			return collect([
				'round_no' => $round->round_no,
				'duration' => $this->time(($round->roundWinnerEvents->last() ?? $round->events->last())->tick, $firstTick, $match),
				'side' => teamNumberToAbbr($round->{"team_{$letter}_side"}),
				'won' => $round->winner_team_id === $team->id,
				'win_reason' => $round->win_reason,
				'own_team_survived' => $round->{"team_{$letter}_survived"},
				'other_team_survived' => $round->{'team_' . otherTeam($letter) . '_survived'},
				'damage' => $damage,
				'real_damage' => $realDamage->sum(fn ($dmg) => min(100, $dmg)),
				'events' => $events,
			]);
		});

		return view('match.player-round-performance', compact('match', 'player', 'rounds'));
	}

	public function downloadDemo(Match $match)
	{
		abort_unless($match->demo_path, 404);

		return Storage::disk('demos')->download($match->demo_path);
	}

	protected function roundToTeamPerformanceData(Round $round, Match $match, Team $teamA, Team $teamB)
	{
		$killOrder = collect();

		$team = [
			'kills' => [],
			'flashes' => [],
			'flashbangs' => 0,
			'damage' => 0,
			'smokes' => 0,
			'molotovs' => 0,
			'money' => 0,
			'equipment_value' => 0,
		];

		$teams = collect(['a' => $team, 'b' => $team, 'z' => $team])->recursive();

		$realDamage = collect([
			'a' => $teamA->players->mapWithKeys(fn ($player) => [$player->id => 0]),
			'b' => $teamB->players->mapWithKeys(fn ($player) => [$player->id => 0]),
			'z' => collect(),
		]);

		$firstTick = ($round->freezeTimeEndedEvents->first() ?? $round->events->first())->tick;

		foreach ($round->killEvents as $killEvent) {
			if ($killEvent->teamkill) {
				continue;
			}

			$attackerTeam = $this->teamOf($killEvent->attacker, $teamA, $teamB);
			$victimTeam = $this->teamOf($killEvent->victim, $teamA, $teamB);

			if ($victimTeam === 'z') {
				continue;
			}

			$time = $this->time($killEvent->tick, $firstTick, $match);

			$killOrder->push(collect([
				'index_within_round' => $killEvent->index_within_round,
				// use the opposite team of the victim so that deaths by bomb don't show up as a death for the enemies
				'team' => otherTeam($victimTeam),
				'time' => $time,
				'headshot' => $killEvent->headshot,
				'weapon' => $killEvent->weapon,
				'attacker' => $killEvent->attacker,
				'victim' => $killEvent->victim,
			]));

			if (! $teams->get($victimTeam)->has('first_death')) {
				$teams->get($victimTeam)->put('first_death', collect([
					'victim' => $killEvent->victim,
					'time' => $time,
					'weapon' => $killEvent->weapon,
				]));
			}

			if ($attackerTeam === 'z') {
				continue;
			}

			$teams->get($attackerTeam)->get('kills')->push($killEvent->weapon);

			if (! $teams->get($attackerTeam)->has('first_kill')) {
				$teams->get($attackerTeam)->put('first_kill', collect([
					'attacker' => $killEvent->attacker,
					'time' => $time,
					'weapon' => $killEvent->weapon,
				]));
			}
		}

		foreach ($round->damageEvents as $damageEvent) {
			if ($damageEvent->friendly_fire) {
				continue;
			}

			$team = $this->teamOf($damageEvent->attacker, $teamA, $teamB);

			if ($team !== 'z') {
				$teams->get($team)->addNum('damage', $damageEvent->damage);
				$realDamage->get(otherTeam($team))->addNum($damageEvent->victim_id, $damageEvent->damage);
			}
		}

		if ($round->plantEvents->isNotEmpty()) {
			$plantEvent = $round->plantEvents->last();
			$time = $this->time($plantEvent->tick, $firstTick, $match);

			$teams->get($this->teamOf($plantEvent->planter, $teamA, $teamB))->put('plant', collect([
				'time' => $time,
				'site' => $plantEvent->site,
			]));

			$killOrder->push(collect([
				'index_within_round' => $plantEvent->index_within_round,
				'type' => 'plant',
				'time' => $time,
			]));

			if ($round->defuseEvents->isNotEmpty()) {
				$defuseEvent = $round->defuseEvents->last();

				$teams->get($this->teamOf($defuseEvent->defuser, $teamA, $teamB))->put('defuse', collect([
					// time: defused x seconds after the bomb was planted
					'time' => $this->time($defuseEvent->tick, $plantEvent->tick, $match),
					'site' => $defuseEvent->site,
				]));

				$killOrder->push(collect([
					'index_within_round' => $defuseEvent->index_within_round,
					'type' => 'defuse',
					'time' => $this->time($defuseEvent->tick, $firstTick, $match),
				]));
			}
		}

		foreach ($round->smokeThrownEvents as $smokeEvent) {
			$team = $this->teamOf($smokeEvent->thrower, $teamA, $teamB);
			$teams->get($team)->addNum('smokes', 1);
		}

		foreach ($round->molotovThrownEvents as $molotovEvent) {
			$team = $this->teamOf($molotovEvent->thrower, $teamA, $teamB);
			$teams->get($team)->addNum('molotovs', 1);
		}

		foreach ($round->flashbangThrownEvents as $flashbangEvent) {
			$team = $this->teamOf($flashbangEvent->thrower, $teamA, $teamB);
			$teams->get($team)->addNum('flashbangs', 1);
		}

		foreach ($round->flashedEvents as $flashedEvent) {
			if ($flashedEvent->teamflash) {
				continue;
			}

			$team = $this->teamOf($flashedEvent->attacker, $teamA, $teamB);

			if ($team === 'z') {
				continue;
			}

			if ($teams->get($team)->get('flashes')->has($flashedEvent->flashbang_entity_id)) {
				$teams->get($team)->get('flashes')->get($flashedEvent->flashbang_entity_id)
					->addNum('enemies_flashed', 1)
					->addNum('duration', $flashedEvent->duration);
			} else {
				$teams->get($team)->get('flashes')->put($flashedEvent->flashbang_entity_id, collect([
					'time' => $this->time($flashedEvent->tick, $firstTick, $match),
					'tick' => $flashedEvent->tick,
					'thrower' => $flashedEvent->attacker,
					'enemies_flashed' => 1,
					'duration' => $flashedEvent->duration,
				]));
			}
		}

		foreach ($round->moneyEquipmentEvents as $moneyEvent) {
			$teams->get($this->teamOf($moneyEvent->player, $teamA, $teamB))
				->addNum('money', $moneyEvent->money_remaining)
				->addNum('equipment_value', $moneyEvent->equipment_value);
		}

		foreach ($round->botTakeoverEvents as $botTakeoverEvent) {
			if ($team !== 'z') {
				$killOrder->push(collect([
					'index_within_round' => $botTakeoverEvent->index_within_round,
					'type' => 'bot_takeover',
					'team' => $this->teamOf($botTakeoverEvent->human, $teamA, $teamB),
					'time' => $this->time($botTakeoverEvent->tick, $firstTick, $match),
					'human' => $botTakeoverEvent->human,
					'bot' => $botTakeoverEvent->bot,
				]));
			}
		}

		$winner = null;

		if ($round->winner_team_id === $teamA->id) {
			$winner = 'a';
		} elseif ($round->winner_team_id === $teamB->id) {
			$winner = 'b';
		}

		return collect([
			'round_no' => $round->round_no,
			'win_reason' => $round->win_reason,
			'duration' => $this->time(($round->roundWinnerEvents->last() ?? $round->events->last())->tick, $firstTick, $match),
			'kill_order' => $killOrder->sortBy('index_within_round'),
		])->merge($teams->map(function ($team, $letter) use ($realDamage, $round, $winner) {
			$enemiesFlashed = 0;
			$enemiesFlashedDuration = 0;
			$longestFlash = $team->get('flashes')->first();

			foreach ($team->get('flashes') as $flash) {
				$enemiesFlashed += $flash->get('enemies_flashed');
				$enemiesFlashedDuration += $flash->get('duration');

				if ($flash->get('duration') > $longestFlash->get('duration')) {
					$longestFlash = $flash;
				}
			}

			return $team->merge([
				'winner' => $winner === $letter,
				'side' => $round->{"team_{$letter}_side"},
				'survived' => $round->{"team_{$letter}_survived"},

				'kills' => collect(array_count_values($team->get('kills')->toArray()))->sortDesc(),

				'enemies_flashed' => $enemiesFlashed,
				'enemies_flashed_duration' => $enemiesFlashedDuration,
				'longest_flash' => $longestFlash,
				'real_damage' => $realDamage->get(otherTeam($letter))->sum(fn ($dmg) => min($dmg, 100)),
			]);
		}));
	}

	protected function time(int $tick, int $firstTick, Match $match) : float
	{
		return ($tick - $firstTick) / $match->tickrate;
	}
}
