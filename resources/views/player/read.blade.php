@extends('layouts.app')

@section('document-title')
	{{ $player->display_name }}
@endsection

@section('page-title')
	@if ($player->flag)
		<img src="https://countryflags.io/{{ $player->flag }}/flat/64.png" alt="{{ $player->flag }}" title="{{ $player->flag }}" class="inline-flag">
	@endif

	{{ $player->display_name }}
@endsection

@section('page-title-classes')
	player-name --left
@endsection

@section('content')
	<career-scores
		rows-are="maps"
		series-played="{{ $series->count() }}"
		:steam-data="{{ json_encode([
			'url' => $steamId ? 'https://steamcommunity.com/profiles/' . $steamId->ConvertToUInt64() : null,
			'name' => $player->steam_name,
		]) }}"
		:all-ladders="{{ app('all_ladders')->map->only('id', 'name') }}"
		:all-maps="{{ $maps->mapWithKeys(function ($map) {
			return [$map->id => $map->only('filename', 'display_name')];
		}) }}"
		:all-stats="{{ $stats }}"
	></career-scores>

	<p class="stat-date-note">
		@if (Request::input('alltime') === '1')
			These statistics include data for all matches this player has played.
			<a href="{{ route('player', $player->id) }}">
				only show data for recent matches
			</a>
		@else
			These statistics only include data from series within the last 90 days.
			<a href="{{ route('player', $player->id) }}?alltime=1">
				show data for all matches
			</a>
		@endif
	</p>
@endsection
