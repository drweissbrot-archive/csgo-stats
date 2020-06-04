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
			'ladder', 'teams.players',

			'matches' => function ($matches) {
				$matches->with('map')->withoutKnifeRounds();
			},
		])->latest()->take(10)->get();

		$this->calculateSeriesScores($latestSeries);

		return view('index', compact('latestSeries'));
	}
}
