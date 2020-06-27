<?php

namespace App\Console\Commands;

use App\Map;
use App\Series;
use DB;
use Illuminate\Console\Command;

class FakeMatchCommand extends Command
{
	protected $signature = '
        fake:match
        {series : The id of the series to add a match to}
        {score_a : Team A\'s score}
        {score_b : Team B\'s score}
        {map=x_default : The map that the match was (or would have been) on}
    ';

	protected $description = 'Add a fake match to an existing series';

	public function handle()
	{
		DB::transaction(function () {
			$series = Series::with('matches', 'ladder', 'teams')->findOrFail($this->argument('series'));
			$map = Map::where('name', $this->argument('map'))->firstOrFail();

			$scoreA = $this->argument('score_a');
			$scoreB = $this->argument('score_b');

			$winnerTeam = ($scoreA > $scoreB)
				? $series->teamA
				: (
					($scoreB > $scoreA)
						? $series->teamB
						: null
				);

			$nextIndex = $series->matches->last()->index_within_series + 1;

			$this->info('Adding a fake match to:');
			$this->line("Series: {$series->name}, started {$series->matches->first()->started_at}");
			$this->line("{$series->teamA->name} vs. {$series->teamB->name}");
			$this->line("Existing Matches within Series: {$nextIndex}");
			$this->line("in Ladder: {$series->ladder->name}\n");
			$this->line('Fake Match:');
			$this->line("Map: {$map->display_name} ({$map->name})");
			$this->line("{$series->teamA->name} {$scoreA} - {$scoreB} {$series->teamB->name}");
			$this->line('Winner: ' . ($winnerTeam ? $winnerTeam->name : '(tie)'));

			if (! $this->confirm('Confirm?')) {
				return $this->info('Cancelled.');
			}

			$this->info('Creating the fake match...');

			$series->matches()->create([
				'index_within_series' => $nextIndex,

				'is_knife_round' => false,
				'round_count' => $scoreA + $scoreB,

				'max_rounds' => 30,
				'has_halftime' => true,

				'team_a_score' => $scoreA,
				'team_a_score_first_half' => 0,
				'team_a_score_second_half' => 0,
				'team_a_score_ot' => 0,

				'team_b_score' => $scoreB,
				'team_b_score_first_half' => 0,
				'team_b_score_second_half' => 0,
				'team_b_score_ot' => 0,

				'team_a_started_on' => 'ct',
				'team_b_started_on' => 't',

				'server_name' => 'Match Not Played',
				'tickrate' => 0,
				'ticks' => 0,
				'duration' => 0,

				'started_at' => now(),

				'game_mode' => 1,
				'game_type' => 0,

				'winner_team_id' => optional($winnerTeam)->id,
				'map_id' => $map->id,
			]);

			$this->info('Fake Match created.');
		});
	}
}
