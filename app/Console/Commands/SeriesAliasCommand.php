<?php

namespace App\Console\Commands;

use App\Series;
use DB;
use Illuminate\Console\Command;

class SeriesAliasCommand extends Command
{
	protected $signature = '
		series:alias
		{series : The id of the series}
		{alias}
	';

	protected $description = 'Set an alias for the given series.';

	public function handle()
	{
		DB::transaction(function () {
			$series = Series::findOrFail($this->argument('series'));

			$series->update([
				'alias' => $this->argument('alias'),
			]);

			$this->info('Alias set. Match is now available at ' . route('alias', $series->alias));
		});
	}
}
