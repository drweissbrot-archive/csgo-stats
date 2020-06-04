@extends('layouts.app')

@section('page-title')
	@if ($series->name)
		{{ $series->name }} (BO{{ $series->best_of }})
	@else
		BO{{ $series->best_of }} Series –
		{{ $series->ladder->name }}
	@endif
@endsection

@section('content')
	<div class="cards">
		@if ($matchesExceptKnifeRounds->pluck('rounds')->map->count()->sum() !== $matchesExceptKnifeRounds->sum(fn ($match) => $match->team_a_score + $match->team_b_score))
			<div class="card --alert">
				<div class="title">
					Flawed Data: Round Mismatch
				</div>

				<div class="value">
					The number of counted rounds does not match the scores
				</div>
			</div>
		@endif

		<div class="card">
			<div class="title">
				Ladder
			</div>

			<a href="{{ route('ladder', $series->ladder->id) }}" class="value">
				{{ $series->ladder->name }}
			</a>
		</div>

		<div class="card">
			<div class="title">
				Duration
			</div>

			<div class="value">
				@php $duration = $series->matches->sum('duration') @endphp
				{{ gmdate(($duration > 3600) ? 'H:i:s' : 'i:s', $duration) }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Started
			</div>

			<div class="value">
				{{ $series->matches->first()->started_at->tz('Europe/Berlin')->format('D, M j, Y H:i') }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Best of
			</div>

			<div class="value">
				{{ $series->best_of }}
				@if ($series->hasKnifeRound())
					<span title="Series had a Knife Round">
						+ KR
					</span>
				@endif
			</div>
		</div>

		<div class="card">
			<div class="title">
				Rounds
			</div>

			<div class="value">
				{{ $matchesExceptKnifeRounds->pluck('rounds')->flatten(1)->count() }}
			</div>
		</div>

		@if ($series->notes)
			<div class="card">
				<div class="title">
					Series Notes
				</div>

				<div class="value">
					{{ $series->notes }}
				</div>
			</div>
		@endif
	</div>

	@include('partials.matches-list', compact('series'))

	<big-scoreboard
		player-route="{{ route('player', '%') }}"
		:teams-data="{{ $teams }}"
		:icons="{{ json_encode([
			'ct' => (string) mix('images/ct.svg'),
			't' => (string) mix('images/t.svg'),
			'win_bomb' => (string) mix('images/win-by-bomb.svg'),
			'win_defuse' => (string) mix('images/win-by-defuse.svg'),
			'win_elimination' => (string) mix('images/win-by-elimination.svg'),
			'win_timer' => (string) mix('images/win-by-timer.svg'),
		]) }}"
		:show-round-percentages="true"
		:is-series="true"
		:played-round-counts="{{ json_encode($roundsPlayed) }}"
	></big-scoreboard>
@endsection
