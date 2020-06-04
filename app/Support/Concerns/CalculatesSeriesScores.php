<?php

namespace App\Support\Concerns;

use Illuminate\Support\Collection;

trait CalculatesSeriesScores
{
	protected function calculateSeriesScores(Collection $allSeries) : void
	{
		foreach ($allSeries as $series) {
			$series->scores = ($series->matches->count() > 1)
				? [
					$series->teamA->id => $series->matches->where('winner_team_id', $series->teamA->id)->count(),
					$series->teamB->id => $series->matches->where('winner_team_id', $series->teamB->id)->count(),
				] : [
					$series->teamA->id => $series->matches->first()->team_a_score,
					$series->teamB->id => $series->matches->first()->team_b_score,
				];

			if ($series->scores[$series->teamA->id] > $series->scores[$series->teamB->id]) {
				$series->winner = $series->teamA->id;
			} elseif ($series->scores[$series->teamB->id] > $series->scores[$series->teamA->id]) {
				$series->winner = $series->teamB->id;
			}
		}
	}
}
