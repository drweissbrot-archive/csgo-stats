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
	<div class="cards">
		<div class="card">
			<div class="title">
				Steam
			</div>

			<a href="https://steamcommunity.com/profiles/{{ $steamId->ConvertToUInt64() }}" class="value">
				{{ $player->steam_name }}
			</a>
		</div>

		<div class="card">
			<div class="title">
				Series Played
			</div>

			<div class="value">
				{{ $series->count() }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Maps Played
			</div>

			<div class="value">
				{{ $matches->count() }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Rounds Played
			</div>

			<div class="value">
				{{ $matches->sum(fn ($match) => $match->rounds->count()) }}
			</div>
		</div>
	</div>

	<player-scores
		:all-ladders="{{ $ladders->pluck('name') }}"
		:all-maps="{{ $maps->mapWithKeys(function ($map) {
			return [$map->id => $map->only('filename', 'display_name')];
		}) }}"
		:all-stats="{{ $stats }}"
	></player-scores>

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
