@extends('match.read')

@section('scoreboard')
	<div class="filter">
		<a href="{{ route('match', $match->id) }}">
			Scoreboard
		</a>
	</div>

	<div class="vertical-performance-graph team-round-performance-graph">
		<div class="sticky">
			<div class="round --team-names">
				@foreach (['a' => $match->series->teamA, 'b' => $match->series->teamB] as $letter => $team)
					<div class="team --{{ $letter }}">
						<div class="name">
							<div>
								@if ($team->flag)
									<img src="https://countryflags.io/{{ $team->flag }}/flat/64.png" alt="{{ $team->flag }}" title="{{ $team->flag }}">
								@endif

								{{ $team->name ?: 'Team ' . mb_strtoupper($letter) }}
							</div>
						</div>

						@php
							$ownScore = $match->{"team_{$letter}_score"};
							$otherScore = $match->{'team_' . otherTeam($letter) . '_score'};
						@endphp
						<div class="score {{ ($ownScore > $otherScore) ? 'text-green' : (($otherScore > $ownScore) ? 'text-red' : '') }}">
							{{ $match->{"team_{$letter}_score"} }}
						</div>
					</div>
				@endforeach
			</div>

			<div class="round">
				@for ($i = 0; $i < 2; $i++)
					<div class="team @if ($i === 1) --b @endif">
						<div class="kills heading">
							Kills
						</div>

						<div class="first-kill heading">
							First Kill
						</div>

						<div class="first-death heading">
							First Death
						</div>

						<div class="damage heading">
							DMG Total/<wbr>Real
						</div>

						<div class="plant-defuse heading">
							Plant/<wbr>Defuse
						</div>

						<div class="nades-thrown heading">
							Nades Thrown
						</div>

						<div class="flashes heading">
							Enemies Flashed
						</div>

						<div class="best-flash heading">
							Best Flash
						</div>

						<div class="money-eq heading" title="Equipment Value/Money">
							Eq&nbsp;Value/<wbr>Money
						</div>

						<div class="survived-winner heading"></div>
					</div>
				@endfor
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

				@foreach (['a', 'b'] as $letter)
					<div class="team --{{ teamNumberToAbbr($round->get($letter)->get('side')) }} --{{ $letter }} @if ($round->get($letter)->get('winner')) --winner @endif">
						<div class="kills">
							@if ($round->get($letter)->get('kills')->isNotEmpty())
								<div class="table">
									@foreach ($round->get($letter)->get('kills') as $weapon => $kills)
										<div class="row">
											<div class="col">
												<img class="weapon"
													src="{{ mix("images/{$weapon}.svg") }}"
													alt="{{ $weapon }}"
													title="{{ $weapon }}"
												>
											</div>
											<div class="col">
												{{ $kills }}
											</div>
										</div>
									@endforeach
								</div>
							@else
								N/A
							@endif
						</div>

						<div class="first-kill">
							@if ($round->get($letter)->has('first_kill'))
								<div class="name-wrapper">
									<div class="text-ellipsis">
										{{ $round->get($letter)->get('first_kill')->get('attacker')->display_name }}
									</div>
								</div>

								<div>
									<img class="weapon"
										src="{{ mix("images/{$round->get($letter)->get('first_kill')->get('weapon')}.svg") }}"
										alt="{{ $round->get($letter)->get('first_kill')->get('weapon') }}"
										title="{{ $round->get($letter)->get('first_kill')->get('weapon') }}"
									>

									{{ floor($round->get($letter)->get('first_kill')->get('time')) }}s
								</div>
							@else
								N/A
							@endif
						</div>

						<div class="first-death">
							@if ($round->get($letter)->has('first_death'))
								<div class="name-wrapper">
									<div class="text-ellipsis">
										{{ $round->get($letter)->get('first_death')->get('victim')->display_name }}
									</div>
								</div>

								<div>
									<img class="weapon"
										src="{{ mix("images/{$round->get($letter)->get('first_death')->get('weapon')}.svg") }}"
										alt="{{ $round->get($letter)->get('first_death')->get('weapon') }}"
										title="{{ $round->get($letter)->get('first_death')->get('weapon') }}"
									>

									{{ floor($round->get($letter)->get('first_death')->get('time')) }}s
								</div>
							@else
								N/A
							@endif
						</div>

						<div class="damage" title="Total / Real Damage dealt to enemies">
							<div title="Total Damage dealt to enemies">
								{{ $round->get($letter)->get('damage') }}
							</div>

							<div title="Real Damage dealt to enemies">
								{{ $round->get($letter)->get('real_damage') }}
							</div>
						</div>

						<div class="plant-defuse">
							<div class="heading">
								@if ($round->get($letter)->get('side') === 2)
									Plant
								@else
									Defuse
								@endif
							</div>

							@php
								$plantOrDefuse = $round->get($letter)->get('plant') ?? $round->get($letter)->get('defuse')
							@endphp

							@if ($plantOrDefuse)
								<img class="bombsite" src="{{ mix("images/bombsite-{$plantOrDefuse->get('site')}.svg") }}" alt="Bombsite {{ mb_strtoupper($plantOrDefuse->get('site')) }}">

								<div class="time">
									{{ floor($plantOrDefuse->get('time')) }}s
								</div>
							@else
								N/A
							@endif
						</div>

						<div class="nades-thrown">
							<div class="table">
								<div class="row" title="Smokes">
									<div class="col">
										<img class="weapon" src="{{ mix('images/smokegrenade.svg') }}" alt="Smoke">
									</div>

									<div class="col">{{ $round->get($letter)->get('smokes') }}</div>
								</div>

								<div class="row" title="Flashbangs">
									<div class="col">
										<img class="weapon" src="{{ mix('images/flashbang.svg') }}" alt="Flashbang">
									</div>
									<div class="col">{{ $round->get($letter)->get('flashbangs') }}</div>
								</div>

								<div class="row" title="Molotovs/Incendiary Grenades">
									<div class="col">
										<img class="weapon" src="{{ mix('images/molotov.svg') }}" alt="Molotov">
										<img class="weapon" src="{{ mix('images/incgrenade.svg') }}" alt="Incendiary Grenade">
									</div>
									<div class="col">{{ $round->get($letter)->get('molotovs') }}</div>
								</div>
							</div>
						</div>

						<div class="flashes">
							@if ($round->get($letter)->get('flashes')->isNotEmpty())
								{{ $round->get($letter)->get('enemies_flashed') }}
								for {{ round($round->get($letter)->get('enemies_flashed_duration') * 100) / 100 }}s
							@else
								N/A
							@endif
						</div>

						<div class="best-flash">
							@if ($round->get($letter)->get('longest_flash'))
								<div>
									{{ $round->get($letter)->get('longest_flash')->get('enemies_flashed') }} EF
									{{ round($round->get($letter)->get('longest_flash')->get('duration') * 100) / 100 }}s
								</div>

								<div class="name-wrapper">
									<div class="text-ellipsis">
										{{ $round->get($letter)->get('longest_flash')->get('thrower')->display_name }}
									</div>
								</div>

								<span title="tick {{ $round->get($letter)->get('longest_flash')->get('tick') }}">
									@ {{ floor($round->get($letter)->get('longest_flash')->get('time')) }}s
								</span>
							@else
								N/A
							@endif
						</div>

						<div class="survived-win">
							<survived-svg survived="{{ $round->get($letter)->get('survived') }}"></survived-svg>

							@if ($round->get($letter)->get('winner'))
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

						<div class="kill-order">
							@foreach ($round->get('kill_order') as $kill)
								@if ($kill->get('type') === 'plant')
									<img
										src="{{ mix('images/bomb_icon.svg') }}"
										alt="Bomb Planted"
										title="Bomb Planted @ {{ floor($kill->get('time') * 10) / 10 }}s"
										@if ($round->get($letter)->get('side') !== 2) class="death" @endif
									>
								@elseif ($kill->get('type') === 'defuse')
									<img
										src="{{ mix('images/win-by-defuse.svg') }}"
										alt="Bomb Defused"
										title="Bomb Defused @ {{ floor($kill->get('time') * 10) / 10 }}s"
										@if ($round->get($letter)->get('side') !== 3) class="death" @endif
									>
								@elseif ($kill->get('type') === 'bot_takeover')
									<img
										src="{{ mix('images/switch-teams-dead.svg') }}"
										alt="Bot Takeover"
										title="Bot Takeover: {{ $kill->get('human')->display_name }} → {{ optional($kill->get('bot'))->display_name ?? 'BOT' }} @ {{ floor($kill->get('time') * 10) / 10 }}s"
										@if ($kill->get('team') !== $letter) class="death" @endif
									>
								@else
									<img
										src="{{ mix('images/elimination' . (($kill->get('headshot')) ? '-headshot' : '') . '.svg') }}"
										@if ($kill->get('team') === $letter)
											class="kill"
											alt="Kill"
											title="Kill: {{ $kill->get('attacker')->display_name }}{{ $kill->get('headshot') ? ' (HS)' : '' }} @ {{ floor($kill->get('time') * 10) / 10 }}s {{ $kill->get('weapon') }}"
										@else
											class="death"
											alt="Death"
											title="Death: {{ $kill->get('victim')->display_name }}{{ $kill->get('headshot') ? ' (HS)' : '' }} @ {{ floor($kill->get('time') * 10) / 10 }}s {{ $kill->get('weapon') }}"
										@endif
									>
								@endif
							@endforeach
						</div>

						<div class="money-eq" title="Equipment Value/Money">
							<div>
								@if ($round->get('round_no') === 0 || $round->get('round_no') === 15)
									PISTOL
								@elseif ($round->get($letter)->get('equipment_value') / $match->playersPerTeam() >= 2500)
									{{-- 2500 USD per Person = 12500 USD in a 5 person team --}}
									BUY
								@elseif ($round->get($letter)->get('equipment_value') >= $round->get($letter)->get('money'))
									FORCE
								@else
									ECO
								@endif
							</div>

							<div title="Equipment Value">
								$&nbsp;{{
									($round->get($letter)->get('equipment_value') >= 1000)
										? round($round->get($letter)->get('equipment_value') / 100) / 10 . 'k'
										: $round->get($letter)->get('equipment_value')
								}}
							</div>

							<div title="Money">
								$&nbsp;{{
									($round->get($letter)->get('money') > 1000)
										? round($round->get($letter)->get('money') / 100) / 10 . 'k'
										: $round->get($letter)->get('money')
								}}
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@endforeach
	</div>
@endsection
