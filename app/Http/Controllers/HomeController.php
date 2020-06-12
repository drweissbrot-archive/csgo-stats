<?php

namespace App\Http\Controllers;

use App\Series;
use App\Support\Concerns\CalculatesSeriesScores;

class HomeController extends Controller
{
	use CalculatesSeriesScores;

	public function __invoke()
	{
		$latestSeries = Series::with([
			'ladder',

			'teams.players' => function ($players) {
				$players->orderBy('display_name');
			},

			'matches' => function ($matches) {
				$matches->with('map')->withoutKnifeRounds();
			},
		])->latest()->take(10)->get()->sortByDesc('matches.0.started_at');

		$this->calculateSeriesScores($latestSeries);

		return view('index', compact('latestSeries'));
	}
}
