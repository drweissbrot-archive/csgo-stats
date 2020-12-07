@extends('layouts.app')

@section('document-title')
	{{ $team->name ?: 'Unnamed Team' }}
@endsection

@section('page-title')
	@if ($team->flag)
		<img
			class="inline-flag"
			src="https://flagcdn.com/h60/{{ $team->flag }}.png"
			srcset="https://flagcdn.com/h120/{{ $team->flag }}.png 2x"
			alt="{{ $team->flag }}"
			title="{{ $team->flag }}"
		>
	@endif

	{{ $team->name ?: 'Unnamed Team' }}
@endsection

@section('page-title-classes')
	player-name --left
@endsection

@section('content')
	<career-scores
		rows-are="players"
		series-played="{{ $team->series->count() }}"
		:all-ladders="{{ app('all_ladders')->map->only('id', 'name') }}"
		:all-maps="{{ $maps->mapWithKeys(function ($map) {
			return [$map->id => $map->only('filename', 'display_name')];
		}) }}"
		:all-players="{{ $team->players->mapWithKeys(function ($player) {
			return [$player->id => [
				'display_name' => $player->display_name,
				'flag' => $player->flag,
				'url' => route('player', $player->id),
			]];
		}) }}"
		:all-stats="{{ $stats }}"
	></career-scores>

	<p class="stat-date-note">
		@if (Request::input('alltime') === '1')
			These statistics include data for all matches this team has played.
			<a href="{{ route('team', $team->id) }}">
				only show data for recent matches
			</a>
		@else
			These statistics only include data from series within the last 90 days.
			<a href="{{ route('team', $team->id) }}?alltime=1">
				show data for all matches
			</a>
		@endif
	</p>
@endsection
