@extends('layouts.app')

@section('page-title')
	{{ $ladder->name }}
@endsection

@section('content')
	<div class="cards">
		<div class="card">
			<div class="title">
				Series Played
			</div>

			<div class="value">
				{{ $ladder->series->count() }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Maps Played
			</div>

			<div class="value">
				{{ $ladder->series->pluck('matches')->flatten(1)->count() }}
			</div>
		</div>

		<div class="card">
			<div class="title">
				Rounds Played
			</div>

			<div class="value">
				{{ $ladder->series->pluck('matches')->flatten(1)->sum(function ($match) { return $match->team_a_score + $match->team_b_score; }) }}
			</div>
		</div>
	</div>

	@foreach ($ladder->series as $series)
		@include('partials.series')
	@endforeach
@endsection
