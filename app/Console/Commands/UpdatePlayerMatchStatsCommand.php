<?php

namespace App\Console\Commands;

use App\Match;
use DB;
use Facades\App\Support\ScoreBuilder;
use Illuminate\Console\Command;

class UpdatePlayerMatchStatsCommand extends Command
{
	protected $signature = 'stats:update {matchId}';

	protected $description = 'Recalculate PlayerMatchStats for the given match';

	public function handle()
	{
		DB::transaction(function () {
			$match = Match::find($this->argument('matchId'));

			$match->playerMatchStats()->delete();

			ScoreBuilder::buildScores($match);
		});
	}
}
