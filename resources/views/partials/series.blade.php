<div class="series">
	<a href="{{ route('series', $series->id) }}" class="series-name">
		<h2>{{ $series->name ?? $series->ladder->name }}</h2>

		<div class="small">
			<div>
				Best of {{ $series->best_of }} Series
			</div>

			<div>
				{{ $series->matches->first()->started_at->tz('Europe/Berlin')->format('D, M j, Y H:i') }}
			</div>
		</div>
	</a>

	@foreach ([$series->teamA, $series->teamB] as $team)
		<a href="{{ route('team', $team->id) }}" class="team-name {{ $loop->first ? '--a' : '--b' }}">
			<div>
				@if ($team->flag)
					<img src="https://countryflags.io/{{ $team->flag }}/flat/64.png" alt="{{ $team->flag }}" title="{{ $team->flag }}" class="inline-flag">
				@endif

				{{ $team->name ?: ($loop->first ? 'Team A' : 'Team B') }}
			</div>
		</a>

		<div class="players {{ $loop->first ? '--a' : '--b' }}">
			@foreach ($team->players as $player)
				@if ($player->bot) @continue @endif

				<a href="{{ route('player', $player->id) }}" class="player-name">
					@if ($player->flag)
						<img src="https://countryflags.io/{{ $player->flag }}/flat/64.png" alt="{{ $player->flag }}" title="{{ $player->flag }}" class="inline-flag">
					@endif

					{{ $player->display_name }}
				</a>
			@endforeach
		</div>

		<div class="team-score {{ $loop->first ? '--a' : '--b' }} @if ($series->winner === $team->id) text-green @elseif ($series->winner !== null) text-red @endif">
			{{ $series->scores[$team->id] }}
		</div>
	@endforeach

	@include('partials.matches-list', compact('series'))
</div>
