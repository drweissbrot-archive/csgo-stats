<?php

namespace App\Console\Commands;

use App\Ladder;
use DB;
use Facades\App\Support\MatchCreator;
use Facades\App\Support\SeriesCreator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Storage;
use Str;
use Symfony\Component\Process\Process;

class DemoIntakeCommand extends Command
{
	protected $signature = 'demo:import';

	protected $description = 'Check all the demo intake directory for demos, parse them, and add load them into the database.';

	public function handle()
	{
		$ladders = Ladder::all();

		foreach ($ladders as $ladder) {
			$this->info("searching for demos for ladder {$ladder->id} {$ladder->name}");

			$files = collect(Storage::disk('demo_intake')->allFiles($ladder->name))
				->map(function ($file) use ($ladder) {
					return preg_replace('/^' . preg_quote($ladder->name, '/') . '\//u', '', $file);
				})
				->filter(function ($file) {
					return Str::contains($file, '/') && ! Str::contains($file, 'CANNOT IMPORT');
				});

			$ignoreSeries = [];

			foreach ($files as $key => $file) {
				$seriesName = preg_replace('/\/.+$/u', '', $file) ?? '_series_name_';

				if (! Str::contains($file, '/') || ! preg_match('/^BO\d+ (_\d+|[^_].+)\/(' . preg_quote($seriesName, '/') . '\.txt|\d{4}-\d{2}-\d{2} \d{2}[:\.]\d{2}([:\.]\d{2})?( knife)?+\.dem(\.txt)?)$/u', $file)) {
					$this->info("cannot import {$file}, will ignore series {$seriesName}");
					$files->forget($key);
					$ignoreSeries[] = $seriesName;

					if (! Str::contains($file, 'CANNOT IMPORT')) {
						Storage::disk('demo_intake')->put("{$ladder->name}/CANNOT IMPORT " . str_replace('/', ' - ', $file) . '.txt', "file '{$file}' cannot be imported since it is not properly named or not in a properly named directory");
					}
				}
			}

			$files = $files->filter(function ($file) use ($ignoreSeries) {
				foreach ($ignoreSeries as $series) {
					if (Str::startsWith($file, $series)) {
						return false;
					}
				}

				return true;
			});

			$allSeries = $files->mapToGroups(function ($file) {
				[$dir, $name] = explode('/', $file);

				return [$dir => $name];
			});

			foreach ($allSeries as $seriesName => $files) {
				$this->info("importing series {$seriesName}");

				DB::transaction(function () use ($ladder, $seriesName, $files) {
					$this->importSeries($ladder, $seriesName, $files);
				});
			}
		}
	}

	protected function importSeries(Ladder $ladder, string $seriesName, Collection $files)
	{
		[$bestOf, $seriesDisplayName] = explode(' ', $seriesName, 2);
		$bestOf = preg_replace('/\D+/u', '', $bestOf);

		if (Str::startsWith($seriesDisplayName, '_')) {
			$seriesDisplayName = null;
		}

		$seriesNotes = ($files->contains("{$seriesName}.txt"))
			? (Storage::disk('demo_intake')->get("{$ladder->name}/{$seriesName}/{$seriesName}.txt") ?: null)
			: null;

		$series = null;

		$matchNo = 0;

		foreach ($files as $file) {
			if (Str::endsWith($file, '.dem')) {
				$this->info("importing demo {$file}");

				$process = new Process([
					'node',
					base_path('demo-parser.js'),
					Storage::disk('demo_intake')->path("{$ladder->name}/{$seriesName}/{$file}"),
					config('owner.steam_ids'),
				]);

				$process->setTimeout(180);
				$process->mustRun();
				$demoJson = $process->getOutput();
				$demo = collect(json_decode($demoJson))->recursive();

				if (! $series) {
					$series = SeriesCreator::fromDemo($demo, $ladder, $bestOf, $seriesDisplayName, $seriesNotes);
				}

				$isKnife = Str::endsWith($file, ' knife.dem');
				$startedAt = new Carbon(
					collect(preg_split('/[ _:]|\.dem/u', $file, 3))->forget(2)->join(' ')
				);

				$matchNotes = ($files->contains("{$file}.txt"))
					? (Storage::disk('demo_intake')->get("{$ladder->name}/{$seriesName}/{$file}.txt") ?: null)
					: null;

				$match = MatchCreator::fromDemo($demo, $series, $matchNo++, $isKnife, $startedAt, null, $matchNotes);

				Storage::disk('demos')->put("{$match->id}.dem.gz", gzencode(
					Storage::disk('demo_intake')->get("{$ladder->name}/{$seriesName}/{$file}")
				));

				$match->demo_path = "{$match->id}.dem.gz";
				$match->save();
			}
		}

		Storage::disk('demo_intake')->delete("{$ladder->name}/{$seriesName}/{$seriesName}.txt");

		// only delete the files after all demos have been saved to the database to prevent data loss
		foreach ($files as $file) {
			Storage::disk('demo_intake')->delete("{$ladder->name}/{$seriesName}/{$file}", "{$ladder->name}/{$seriesName}/{$file}.txt");
		}

		if (empty(Storage::disk('demo_intake')->files("{$ladder->name}/{$seriesName}"))) {
			Storage::disk('demo_intake')->deleteDirectory("{$ladder->name}/{$seriesName}");
		}
	}
}
