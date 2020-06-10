<?php

namespace App\Support;

use App\Ladder;
use App\Series;
use App\Support\Concerns\FindsAndCreatesPlayers;
use App\Team;
use DB;
use Illuminate\Support\Collection;

class SeriesCreator
{
	use FindsAndCreatesPlayers;

	public function fromDemo(Collection $demo, Ladder $ladder, int $bestOf, string $name = null, string $notes = null) : Series
	{
		return DB::transaction(function () use ($demo, $ladder, $bestOf, $name, $notes) {
			$players = $this->findOrCreatePlayers($demo);
			[$teamA, $teamB] = $this->setUpTeams($demo, $players);

			$series = $ladder->series()->create([
				'best_of' => $bestOf,
				'name' => $name,
				'notes' => $notes,
			]);

			$series->teams()->attach($teamA->id, ['letter' => 'a']);
			$series->teams()->attach($teamB->id, ['letter' => 'b']);

			return $series;
		});
	}

	protected function setUpTeams(Collection $demo, Collection $players) : array
	{
		$t = $demo->get('teams')->get('t');
		$ct = $demo->get('teams')->get('ct');

		$sideA = 'ct';

		if ($t->get('players')->contains(config('owner.steam_id'))) {
			$sideA = 't';
		} elseif ($ct->get('players')->contains(config('owner.steam_id'))) {
			$sideA = 'ct';
		} elseif (in_array($t->get('name'), explode(',', config('owner.teams')))) {
			$sideA = 't';
		} elseif (in_array($ct->get('name'), explode(',', config('owner.teams')))) {
			$sideA = 'ct';
		} elseif ($t->get('flag') === config('owner.flag') && $ct->get('flag') !== config('owner.flag')
			|| $t->get('score') > $ct->get('score')) {
			$sideA = 't';
		}

		$teamA = $this->findOrCreateTeamAndAssignPlayers($demo, $players, $sideA);
		$teamB = $this->findOrCreateTeamAndAssignPlayers($demo, $players, otherTeam($sideA));

		return [$teamA, $teamB];
	}

	protected function findOrCreateTeamAndAssignPlayers(Collection $demo, Collection $players, string $side) : Team
	{
		$team = $this->findExistingTeam($demo->get('teams')->get($side), $players);

		if (! $team) {
			$team = Team::create([
				'name' => $demo->get('teams')->get($side)->get('name'),
				'flag' => $demo->get('teams')->get($side)->get('flag'),
			]);
		}

		$team->players()->attach(
			$players->only($demo->get('teams')->get($side)->get('players'))->pluck('id')
				->diff($team->players->pluck('id'))
		);

		return $team;
	}

	protected function findExistingTeam(Collection $teamData, Collection $players) : ?Team
	{
		$team = Team::with('players')->withCount('players')->having('players_count', $teamData->get('players')->count());

		foreach ($teamData->get('players') as $steamId) {
			if (! $players->get($steamId)) {
				continue;
			}

			$team->whereHas('players', function ($query) use ($players, $steamId) {
				$query->where('players.id', $players->get($steamId)->id);
			});
		}

		return $team->first();
	}
}
