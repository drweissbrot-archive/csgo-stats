@extends('match.read')

@section('scoreboard')
	<div class="filter">
		<a href="{{ route('match', $match->id) }}">Scoreboard</a>
		–
		<a href="{{ route('match.team-round-performance', $match->id) }}">Team Round Performance</a>
	</div>

	<div class="vertical-performance-graph player-round-performance-graph">
		<div class="sticky">
			<div class="round --team-names">
				<div class="team --own">
					<div class="events">
						<div class="event heading" title="Damage Total/Real">
							DMG Total/<wbr>Real
						</div>

						<div class="event --player-name player-name">
							@if ($player->flag)
								<img src="https://countryflags.io/{{ $player->flag }}/flat/64.png" alt="{{ $player->flag }}" title="{{ $player->flag }}" class="inline-flag">
							@endif
							{{ $player->display_name }}’s Performance
						</div>
					</div>
				</div>
			</div>
		</div>

		{{-- empty div so the next row is not the same background color as the last row within sticky --}}
		<div></div>

		@foreach ($rounds as $round)
			<div class="round">
				<div class="round-meta">
					<div class="number">
						#{{ $round->get('round_no') + 1 }}
					</div>

					<div class="duration">
						{{ round($round->get('duration')) }}&nbsp;sec
					</div>
				</div>

				<div class="team --other --{{ otherTeam($round->get('side')) }} @if (! $round->get('won')) --winner @endif">
					@if (! $round->get('won'))
						<div class="won-the-round heading">
							Enemy Team
							<br>
							won the Round
						</div>
					@endif

					<div class="survived-win">
						<survived-svg survived="{{ $round->get('other_team_survived') }}"></survived-svg>

						@if (! $round->get('won'))
							<round-win-reason
								:icons="{{ json_encode([
									'win_bomb' => (string) mix('images/win-by-bomb.svg'),
									'win_defuse' => (string) mix('images/win-by-defuse.svg'),
									'win_elimination' => (string) mix('images/win-by-elimination.svg'),
									'win_surrender' => (string) mix('images/win-by-surrender.svg'),
									'win_timer' => (string) mix('images/win-by-timer.svg'),
								]) }}"
								:reason="{{ $round->get('win_reason') }}"
							></round-win-reason>
						@else
							<div class="img-spacer"></div>
						@endif
					</div>
				</div>

				<div class="team --own --{{ $round->get('side') }} @if ($round->get('won')) --winner @endif">
					<div class="survived-win">
						<survived-svg survived="{{ $round->get('own_team_survived') }}"></survived-svg>

						@if ($round->get('won'))
							<round-win-reason
								:icons="{{ json_encode([
									'win_bomb' => (string) mix('images/win-by-bomb.svg'),
									'win_defuse' => (string) mix('images/win-by-defuse.svg'),
									'win_elimination' => (string) mix('images/win-by-elimination.svg'),
									'win_surrender' => (string) mix('images/win-by-surrender.svg'),
									'win_timer' => (string) mix('images/win-by-timer.svg'),
								]) }}"
								:reason="{{ $round->get('win_reason') }}"
							></round-win-reason>
						@else
							<div class="img-spacer"></div>
						@endif
					</div>

					<div class="events">
						<div class="event" title="Total / Real Damage dealt to enemies">
							<div title="Total Damage dealt to enemies">
								{{ $round->get('damage') }}
							</div>

							<div title="Real Damage dealt to enemies">
								{{ $round->get('real_damage') }}
							</div>
						</div>

						@foreach ($round->get('events') as $event)
							@if ($event->get('type') === 'kill')
								<div class="event" title="Kill">
									<div>
										@if ($event->get('attacker_flashed'))
											<img class="weapon" src="{{ mix('images/blind_kill.svg') }}" alt="Attacker flashed">
										@endif
										{{ round($event->get('time')) }}s
									</div>

									@include('match.player-round-performance-weapon-and-kill-icons')

									<div class="name-wrapper">
										<div class="text-ellipsis">
											{{ $event->get('victim')->display_name }}
										</div>
									</div>
								</div>
							@elseif ($event->get('type') === 'assist')
								<div class="event" title="{{ $event->get('flash_assist') ? 'Flash Assist' : 'Assist' }}">
									<div>
										{{ round($event->get('time')) }}s

										@if ($event->get('flash_assist'))
											<img class="weapon" src="{{ mix('images/flashbang_assist.svg') }}" alt="Flash Assist">
										@else
											+
										@endif
									</div>

									<div class="name-wrapper" title="Attacker">
										<div class="text-ellipsis">
											{{ $event->get('attacker')->display_name }}
										</div>
									</div>

									<div class="name-wrapper" title="Victim">
										<div class="text-ellipsis">
											{{ $event->get('victim')->display_name }}
										</div>
									</div>
								</div>
							@elseif ($event->get('type') === 'death')
								<div class="event --death" title="Death">
									<div>
										@if ($event->get('attacker_flashed'))
											<img class="weapon" src="{{ mix('images/blind_kill.svg') }}" alt="Attacker flashed">
										@endif
										{{ round($event->get('time')) }}s
										<img class="weapon" src="{{ mix('images/elimination.svg') }}" alt="Death">
									</div>

									@include('match.player-round-performance-weapon-and-kill-icons')

									<div class="name-wrapper">
										<div class="text-ellipsis">
											@if ($event->get('weapon') === 'planted_c4')
												Planted Bomb
											@else
												{{ $event->get('attacker')->display_name }}
											@endif
										</div>
									</div>
								</div>
							@elseif ($event->get('type') === 'plant' || $event->get('type') === 'defuse')
								<div class="event">
									{{ round($event->get('time')) }}s

									<img class="bombsite" src="{{ mix("images/bombsite-{$event->get('site')}.svg") }}" alt="Bombsite {{ mb_strtoupper($event->get('site')) }}">

									<div class="heading">
										@if ($event->get('type') === 'plant')
											Plant
										@else
											Defuse
										@endif
									</div>
								</div>
							@elseif ($event->get('type') === 'flashbang')
								<div class="event" title="Flashbang">
									<div>
										{{ round($event->get('time')) }}s
										<img class="weapon" src="{{ mix('images/flashbang.svg') }}" alt="Flashbang">
									</div>

									<div title="Enemies flashed">
										{{ $event->get('enemies_flashed') }} EF
										{{ round($event->get('enemies_flashed_duration') * 100) / 100 }}s
									</div>

									<div title="Teammates flashed">
										{{ $event->get('teammates_flashed') }} TF
										{{ round($event->get('teammates_flashed_duration') * 100) / 100 }}s
									</div>
								</div>
							@elseif ($event->get('type') === 'bot_takeover')
								<div class="event" title="Bot Takeover">
									<div>
										{{ round($event->get('time')) }}s
										<img class="weapon" src="{{ mix('images/switch-teams-dead.svg') }}" alt="Bot Takeover">
									</div>

									<div>
										{{ $event->get('bot')->display_name }}
									</div>
								</div>
							@elseif ($event->get('type') === 'item_pickup')
								<div class="event" title="Picked Up Item">
									<div>
										{{ round($event->get('time')) }}s
									</div>

									<div>
										<img class="weapon" src="{{ mix("images/{$event->get('item')}.svg") }}" alt="{{ $event->get('item') }}" title="{{ $event->get('item') }}">
									</div>
								</div>
							@endif
						@endforeach
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endsection
