<?php

namespace App\Http\Controllers;

use App\Ladder;
use App\Support\Concerns\CalculatesSeriesScores;

class LadderController extends Controller
{
	use CalculatesSeriesScores;

	public function read(Ladder $ladder)
	{
		$ladder->load([
			'series.ladder',

			'series.teams.players' => function ($players) {
				$players->orderBy('display_name');
			},

			'series.matches' => function ($matches) {
				$matches->withoutKnifeRounds()->with('map');
			},
		]);

		$ladder->series = $ladder->series->sortByDesc(function ($series) {
			return $series->matches->first()->started_at;
		});

		$this->calculateSeriesScores($ladder->series);

		return view('ladder.read', compact('ladder'));
	}
}
