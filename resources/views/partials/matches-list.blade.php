<div class="matches-list">
	@foreach ($series->matches as $match)
		<a href="{{ route('match', $match->id) }}" class="match map --{{ $match->map->filename }}">
			<div class="inner">
				<div class="score">
					<span class="{{ $match->team_a_score > $match->team_b_score ? 'text-green' : ($match->team_a_score < $match->team_b_score ? 'text-red' : '') }}">
						{{ $match->team_a_score }}
					</span> –
					<span class="{{ $match->team_b_score > $match->team_a_score ? 'text-green' : ($match->team_b_score < $match->team_a_score ? 'text-red' : '') }}">
						{{ $match->team_b_score }}
					</span>
				</div>

				<div class="name">
					{{ $match->map->display_name }}
					@if ($match->is_knife_round)
						(Knife)
					@endif
				</div>
			</div>
		</a>
	@endforeach
</div>
