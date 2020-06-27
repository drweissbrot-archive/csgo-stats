<?php

namespace App\Http\Controllers;

use App\Ladder;
use App\Map;
use App\Match;

class StatusController extends Controller
{
	public function __invoke()
	{
		$ladders = Ladder::all()->each->load([
			'series' => function ($series) {
				$series->with([
					'matches' => function ($matches) {
						$matches->withoutKnifeRounds()->orderBy('started_at');
					},
				])->latest()->limit(1);
			},
		]);

		$matchesWithRoundMismatch = Match::select('id', 'started_at', 'series_id', 'map_id', 'team_a_score', 'team_b_score')
			->where('map_id', '!=', Map::whereName('x_default')->first()->id)
			->with([
				'series.ladder', 'map',

				'rounds' => function ($rounds) {
					$rounds->where('is_counted', false);
				},
			])->withCount([
				'rounds' => function ($rounds) {
					$rounds->onlyCounted();
				},
			])->havingRaw('`rounds_count` != `team_a_score` + `team_b_score`')
			->groupBy('id', 'started_at', 'series_id', 'map_id', 'team_a_score', 'team_b_score')
			->get();

		return view('status', compact('ladders', 'matchesWithRoundMismatch'));
	}
}
