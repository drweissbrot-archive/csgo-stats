<?php

namespace App\Console\Commands;

use App\Ladder;
use DB;
use Facades\App\Support\MatchCreator;
use Facades\App\Support\SeriesCreator;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DemoImportOneCommand extends Command
{
	protected $signature = '
		demo:import-one
		{path : Path to the demo file}
	';

	protected $description = '(Mostly useful for debugging and testing. Should probably not be used in normal operation.) Parse a single demo, and load it into the database. Will not copy or move the demo file to storage.';

	public function handle()
	{
		DB::transaction(function () {
			$this->info("importing demo {$this->argument('path')}");

			$process = new Process([
				'node',
				config('services.demo_parser'),
				$this->argument('path'),
				// config('owner.steam_ids'),
			]);

			$process->setTimeout(180);
			$process->mustRun();
			$demoJson = $process->getOutput();
			$demo = collect(json_decode($demoJson))->recursive();

			$series = SeriesCreator::fromDemo($demo, Ladder::first(), 1, null, null);
			$match = MatchCreator::fromDemo($demo, $series, 0, false, now(), null, null);
			$match->save();

			$this->info('Match available at ' . route('match', $match->id));
		});
	}
}
