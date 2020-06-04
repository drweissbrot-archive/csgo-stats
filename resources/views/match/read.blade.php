@extends('layouts.app')

@section('document-title')
	@if ($match->series->best_of > 1)
		@if ($match->series->hasKnifeRound())
			@if ($match->is_knife_round)
				Knife:
			@else
				#{{ $match->index_within_series }}
			@endif
		@else
			#{{ $match->index_within_series + 1 }}
		@endif
	@endif
	{{ $match->map->display_name }} –
	@if ($match->series->name)
		{{ $match->series->name }}
	@else
		BO{{ $match->series->best_of }} Series
	@endif
@endsection

@section('page-title')
	@if ($match->series->best_of > 1)
		@if ($match->series->hasKnifeRound())
			@if ($match->is_knife_round)
				Knife Round:
			@else
				Match {{ $match->index_within_series }}:
			@endif
		@else
			Match {{ $match->index_within_series + 1 }}:
		@endif
	@endif

	{{ $match->map->display_name }}
@endsection

@section('content')
	<div class="cards">
		@if ($match->rounds->count() !== $match->team_a_score + $match->team_b_score)
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

			<a href="{{ route('ladder', $match->series->ladder->id) }}" class="value">
				{{ $match->series->ladder->name }}
			</a>
		</div>

		@if ($match->series->best_of > 1)
			<div class="card">
				<div class="title">
					BO{{ $match->series->best_of }} Series
				</div>

				<a href="{{ route('series', $match->series->id) }}" class="value">
					{{ $match->series->name }}
				</a>
			</div>
		@endif

		<div class="card">
			<div class="title">
				Played on
			</div>

			<div class="value">
				{{ $match->server_name }}
			</div>
		</div>

		<div class="card" title="Number of ticks and tickrate">
			<div class="title">
				Ticks (Rate)
			</div>

			<div class="value">
				{{ nf($match->ticks) }}
				({{ $match->tickrate }})
			</div>
		</div>

		<div class="card">
			<div class="title">
				Duration
			</div>

			<div class="value">
				{{ gmdate(($match->duration > 3600) ? 'H:i:s' : 'i:s', $match->duration) }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Started
			</div>

			<div class="value">
				{{ $match->started_at->tz('Europe/Berlin')->format('D, M j, Y H:i') }}
			</div>
		</div>

		@if ($match->series->notes)
			<div class="card">
				<div class="title">
					Series Notes
				</div>

				<div class="value">
					{{ $match->series->notes }}
				</div>
			</div>
		@endif

		@if ($match->notes)
			<div class="card">
				<div class="title">
					Match Notes
				</div>

				<div class="value">
					{{ $match->notes }}
				</div>
			</div>
		@endif

		@if ($match->demo_path)
			<div class="card --highlighted" title="{{ $match->demo_path }}">
				<div class="title">
					Demo Available
				</div>

				<a href="{{ route('demo', $match->id) }}" class="value text-ellipsis">
					Download (gzipped)
				</a>
			</div>
		@endif
	</div>

	<div class="map --hero --{{ $match->map->filename }}"></div>

	@hasSection ('scoreboard')
		@yield('scoreboard')
	@else
		<big-scoreboard
			player-route="{{ route('player', '%') }}"
			team-round-performance-route="{{ route('match.team-round-performance', $match->id) }}"
			player-round-performance-route="{{ route('match.player-round-performance', [$match->id, '%']) }}"
			:teams-data="{{ $teams }}"
			:rounds="{{ $rounds }}"
			:played-round-counts="{{ json_encode($roundsPlayed) }}"
			:icons="{{ json_encode([
				'ct' => (string) mix('images/ct.svg'),
				't' => (string) mix('images/t.svg'),
				'win_bomb' => (string) mix('images/win-by-bomb.svg'),
				'win_defuse' => (string) mix('images/win-by-defuse.svg'),
				'win_elimination' => (string) mix('images/win-by-elimination.svg'),
				'win_surrender' => (string) mix('images/win-by-surrender.svg'),
				'win_timer' => (string) mix('images/win-by-timer.svg'),
			]) }}"
		></big-scoreboard>
	@endif
@endsection
