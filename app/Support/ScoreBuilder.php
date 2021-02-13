<?php

namespace App\Support;

use App\CsMatch;
use App\DamageEvent;
use App\DefuseEvent;
use App\FlashedEvent;
use App\FreezeTimeEndedEvent;
use App\KillEvent;
use App\MvpEvent;
use App\PlantEvent;
use App\Player;
use App\RoundWinnerEvent;
use Illuminate\Support\Collection;

class ScoreBuilder
{
	protected const GRENADES = [
		'breachcharge', 'breachcharge_projectile', 'bumpmine', 'controldrone', 'dronegun', 'exojump', 'firebomb', 'frag_grenade', 'hegrenade', 'incgrenade', 'inferno', 'missile', 'molotov', 'prop_exploding_barrel', 'radarjammer', 'stomp_damage', 'survival_safe', 'tagrenade',
	];

	public function buildScores(CsMatch $match)
	{
		$phase = [
			'round' => 0,
			'a' => [$match->team_a_started_on, 'regulation'],
			'b' => [$match->team_b_started_on, 'regulation'],
			'z' => ['unknown', 'regulation'],
		];

		$teamA = $match->series->teamA->players;
		$teamB = $match->series->teamB->players;
		$teamZ = Player::where('steam_id', 'unknown_user')->get();
		$teams = ['a' => $teamA, 'b' => $teamB];

		$players = $teamA->mapWithKeys(function ($player) {
			return $this->playerScores($player, 'a');
		})->union($teamB->mapWithKeys(function ($player) {
			return $this->playerScores($player, 'b');
		}))->union($teamZ->mapWithKeys(function ($player) {
			return $this->playerScores($player, 'z');
		}));

		foreach ($match->rounds as $round) {
			if (! $round->is_counted) {
				continue;
			}

			$round->team_a_survived = $round->team_b_survived = $match->playersPerTeam();

			$phase['round'] = $round->round_no;

			$phase['a'][0] = teamNumberToAbbr($round->team_a_side);
			$phase['b'][0] = teamNumberToAbbr($round->team_b_side);

			if ($round->round_no === 30) {
				$phase['a'][1] = $phase['b'][1] = $phase['z'][1] = 'overtime';
			}

			$firstTick = ($round->events->first(fn ($e) => $e instanceof FreezeTimeEndedEvent) ?? $round->events->first())->tick;
			$lastTick = ($round->roundWinnerEvents->last() ?? $round->events->last())->tick;

			foreach ($players as $player) {
				$this->playerPhase($player, $phase)->first()->get('rounds')->put($round->round_no, collect([
					'kills' => collect(),
					'assists' => 0,
					'survived' => true,
					'traded' => false,
				]));
			}

			$clutches = ['a' => false, 'b' => false];
			$roundEnded = false;

			$events = $round->events;

			if ($round->freezeTimeEndedEvents->isNotEmpty()) {
				$events = $events->skipUntil(fn ($event) => $event instanceof FreezeTimeEndedEvent);
			}

			foreach ($events as $event) {
				if ($event instanceof DamageEvent) {
					$attacker = $players->get($event->attacker_id);

					// we're limiting damage to 100 hp per hit (this is not as accurate as limiting by the hp of the victim, but more reliable when bots are taken over)
					$damage = min(100, $event->damage);

					$this->playerPhase($attacker, $phase)->each->addNum(
						($event->friendly_fire) ? 'team_damage' : 'enemy_damage', $damage
					);

					if (! $event->friendly_fire && in_array($event->weapon, static::GRENADES)) {
						$this->playerPhase($attacker, $phase)->each->addNum('enemy_utility_damage', $damage);
					}
				} elseif ($event instanceof DefuseEvent) {
					$defuser = $players->get($event->defuser_id);
					$this->playerPhase($defuser, $phase)->each->addNum('defuses', 1);
				} elseif ($event instanceof FlashedEvent) {
					$attacker = $players->get($event->attacker_id);

					$this->playerPhase($attacker, $phase)->each->addNum(
						($event->teamflash) ? 'teammates_flashed' : 'enemies_flashed', 1
					);

					$this->playerPhase($attacker, $phase)->each->addNum(
						($event->teamflash) ? 'teammates_flashed_duration' : 'enemies_flashed_duration',
						$event->duration
					);
				} elseif ($event instanceof KillEvent) {
					// Attacker
					$attacker = $players->get($event->attacker_id);

					$this->playerPhase($attacker, $phase)->each->addNum(
						($event->teamkill) ? 'team_kills' : 'enemy_kills', 1
					);

					// note who they killed and when (so that we can determine if the killed player was traded)
					$this->playerPhase($attacker, $phase)->first()->get('rounds')->last()->get('kills')->put($event->victim_id, $event->tick);

					if (! $event->teamkill) {
						$this->playerPhase($attacker, $phase)->each->addNum("enemy_kills_{$event->weapon}", 1);

						if ($event->headshot) {
							$this->playerPhase($attacker, $phase)->each->addNum('enemy_headshot_kills', 1);
						}
					}

					// Victim
					if ($victim = $players->get($event->victim_id)) {
						$this->playerPhase($victim, $phase)->each->addNum('deaths', 1);
						$this->playerPhase($victim, $phase)->first()->get('rounds')->last()->put('survived', false);

						$this->playerPhase($victim, $phase)->each->addNum(
							'time_alive_ms',
							($event->tick - $firstTick) / $match->tickrate,
						);

						$round->{"team_{$victim->get('team')}_survived"}--;

						if (array_key_exists($victim->get('team'), $clutches) && $clutches[$victim->get('team')]) {
							$clutches[$victim->get('team')]->put('died', true);
						}

						// Trades (Victim and Attacker)
						foreach ($this->playerPhase($victim, $phase)->first()->get('rounds')->last()->get('kills') as $killed => $tick) {
							// consider people traded if their killer is killed within 10 seconds
							if ($tick >= ($event->tick - 10 * $match->tickrate)) {
								$this->playerPhase($players->get($killed), $phase)->each->addNum('deaths_traded', 1);

								$this->playerPhase($players->get($killed), $phase)->first()->get('rounds')->last()->put('traded', true);

								if (! $event->teamkill) {
									$this->playerPhase($attacker, $phase)->each->addNum('enemy_trade_kills', 1);
								}
							}
						}
					}

					// Clutches
					foreach (['a', 'b'] as $letter) {
						if ($round->{"team_{$letter}_survived"} !== 1 || $round->{'team_' . otherTeam($letter) . '_survived'} === 0) {
							continue;
						}

						if ($clutches[$letter] === false && ! $roundEnded) {
							$clutches[$letter] = collect([
								'vs' => $round->{'team_' . otherTeam($letter) . '_survived'},
								'kills' => 0,
								'won' => false,
								'died' => false,

								'player' => $teams[$letter]->first(function ($player) use ($phase, $players, $round) {
									return $this->playerPhase($players->get($player->id), $phase)->first()->get('rounds')->get($round->round_no)->get('survived');
								}),
							]);
						} elseif ($attacker->get('team') === $letter && $clutches[$letter]) {
							$clutches[$letter]->addNum('kills', 1);
						}
					}

					// Assister
					if ($assister = ($event->assister_id) ? $players->get($event->assister_id) : null) {
						$this->playerPhase($assister, $phase)->each->addNum(
							($event->team_assist)
								? (($event->flash_assist) ? 'team_flash_assists' : 'team_assists')
								: (($event->flash_assist) ? 'enemy_flash_assists' : 'enemy_assists'), 1
						);

						if (! $event->team_assist && ! $event->flash_assist) {
							$this->playerPhase($assister, $phase)->first()->get('rounds')->last()->addNum('assists', 1);
						}
					}
				} elseif ($event instanceof MvpEvent) {
					$mvp = $players->get($event->mvp_id);
					$this->playerPhase($mvp, $phase)->each->addNum('mvps', 1);
				} elseif ($event instanceof PlantEvent) {
					$planter = $players->get($event->planter_id);
					$this->playerPhase($planter, $phase)->each->addNum('plants', 1);
				} elseif ($event instanceof RoundWinnerEvent) {
					$roundEnded = true;

					foreach (['a', 'b'] as $letter) {
						if ($clutches[$letter] === false) {
							continue;
						}

						if ($event->winner_side === $round->{"team_{$letter}_side"}) {
							$clutches[$letter]->put('won', true);
						}
					}
				}
			}

			$maxAliveTime = ($lastTick - $firstTick) / $match->tickrate;

			foreach ($players as $player) {
				$this->playerPhase($player, $phase)->each->addNum('max_alive_time_ms', $maxAliveTime);

				// for players that survived, add the total round time
				if ($this->playerPhase($player, $phase)->first()->get('rounds')->get($round->round_no)->get('survived')) {
					$this->playerPhase($player, $phase)->each->addNum('time_alive_ms', $maxAliveTime);
				}
			}

			foreach ($clutches as $clutch) {
				if ($clutch === false) {
					continue;
				}

				$this->playerPhase($players->get($clutch->get('player')->id), $phase)
					->each(function ($phase) use ($clutch) {
						$str = "one_vs_{$clutch->get('vs')}_";

						$phase->addNum("{$str}scenarios", 1)
							->addNum("{$str}wins", $clutch->get('won') ? 1 : 0)
							->addNum("{$str}kills", $clutch->get('kills'))
							->addNum("{$str}deaths", $clutch->get('died') ? 1 : 0);
					});
			}

			$round->team_a_survived = max($round->team_a_survived, 0);
			$round->team_b_survived = max($round->team_b_survived, 0);

			if ($round->isDirty()) {
				$round->save();
			}
		}

		foreach ($players as $id => $player) {
			if ($player->get('team') === 'z') {
				continue;
			}

			foreach ([['ct', 'regulation'], ['t', 'regulation'], ['ct', 'pistol'], ['t', 'pistol'], ['ct', 'overtime'], ['t', 'overtime']] as [$side, $phase]) {
				$stats = $player->get($phase)->get($side);

				foreach ($stats->get('rounds') as $round) {
					if ($round->get('kills')->isNotEmpty() || $round->get('assists') > 0 || $round->get('survived') || $round->get('traded')) {
						$stats->addNum('kast_rounds', 1);
					}

					$stats->addNum(min($round->get('kills')->count(), 5) . '_kill_rounds', 1);
				}

				$match->playerMatchStats()->create($stats->except('rounds')->merge([
					'player_id' => $id,
					'side' => $side,
					'phase' => $phase,
					'time_alive_ms' => round($stats->get('time_alive_ms') * 1000),
					'max_alive_time_ms' => round($stats->get('max_alive_time_ms') * 1000),
				])->toArray());
			}
		}
	}

	protected function playerScores(Player $player, string $team) : array
	{
		return [$player->id => collect([
			'team' => $team,

			'regulation' => collect([
				'ct' => $this->playerScoresPhase(),
				't' => $this->playerScoresPhase(),
				'unknown' => $this->playerScoresPhase(),
			]),

			'overtime' => collect([
				'ct' => $this->playerScoresPhase(),
				't' => $this->playerScoresPhase(),
				'unknown' => $this->playerScoresPhase(),
			]),

			'pistol' => collect([
				'ct' => $this->playerScoresPhase(),
				't' => $this->playerScoresPhase(),
				'unknown' => $this->playerScoresPhase(),
			]),
		])];
	}

	protected function playerScoresPhase() : Collection
	{
		return collect([
			'rounds' => collect(),

			'enemy_kills' => 0,
			'enemy_assists' => 0,
			'enemy_flash_assists' => 0,
			'deaths' => 0,
			'deaths_traded' => 0,
			'enemy_damage' => 0,
			'enemy_utility_damage' => 0,
			'enemy_trade_kills' => 0,
			'enemy_headshot_kills' => 0,
			'kast_rounds' => 0,
			'0_kill_rounds' => 0,
			'1_kill_rounds' => 0,
			'2_kill_rounds' => 0,
			'3_kill_rounds' => 0,
			'4_kill_rounds' => 0,
			'5_kill_rounds' => 0,
			'enemies_flashed' => 0,
			'enemies_flashed_duration' => 0,
			'plants' => 0,
			'defuses' => 0,
			'mvps' => 0,
			'time_alive_ms' => 0,
			'max_alive_time_ms' => 0,

			'team_kills' => 0,
			'team_assists' => 0,
			'team_flash_assists' => 0,
			'team_damage' => 0,
			'teammates_flashed' => 0,
			'teammates_flashed_duration' => 0,
		]);
	}

	protected function playerPhase(Collection $player, array $phase) : Collection
	{
		[$side, $playerPhase] = $phase[$player->get('team')];

		$phases = collect([
			$player->get($playerPhase)->get($side),
		]);

		if ($phase['round'] === 0 || $phase['round'] === 15) {
			$phases->push($player->get('pistol')->get($side));
		}

		return $phases;
	}
}
