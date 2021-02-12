@extends('layouts.app')

@section('page-title')
	Loss Bonus Buys {{ $title }}
@endsection

@section('content')
	<nav class="loadouts">
		<ul>
			<li>
				<a href="{{ route('loss-bonus-buys.m4a1') }}" title="Competitive M4A1">
					<img src="{{ mix('images/competitive.svg') }}" alt="Competitive">
					M4A1
				</a>
			</li>

			<li>
				<a href="{{ route('loss-bonus-buys.m4a4') }}" title="Competitive M4A4">
					M4A4
				</a>
			</li>

			<li>
				<a href="{{ route('loss-bonus-buys.wingman-m4a1') }}" title="Wingman M4A1">
					<img src="{{ mix('images/scrimcomp2v2.svg') }}" alt="Wingman">
					M4A1
				</a>
			</li>

			<li>
				<a href="{{ route('loss-bonus-buys.wingman-m4a4') }}" title="Wingman M4A4">
					M4A4
				</a>
			</li>
		</ul>
	</nav>

	@foreach (['ct' => 'Counter-Terrorists', 't' => 'Terrorists', 'awp' => 'AWP'] as $abbr => $label)
		<section class="{{ $abbr }}">
			<div class="heading">
				<h2>
					@if ($abbr !== 'awp')
						<img src="{{ mix("images/{$abbr}.svg") }}">
					@endif

					{{ $label }}
				</h2>

				<div class="full-buy">
					$&nbsp;{{ nf($prices["{$abbr}_full"]) }}

					@foreach ($loadouts["{$abbr}_full"] as $item)
						<img class="weapon"
							src="{{ mix("images/{$item}.svg") }}"
							alt="{{ $item }}"
							title="{{ $item }}"
						>
					@endforeach
				</div>

				<div class="desperate">
					$&nbsp;{{ nf($prices["{$abbr}_desperate"]) }}

					@foreach ($loadouts["{$abbr}_desperate"] as $item)
						<img class="weapon"
							src="{{ mix("images/{$item}.svg") }}"
							alt="{{ $item }}"
							title="{{ $item }}"
						>
					@endforeach
				</div>
			</div>

			<div class="cards">
				@for ($i = 0; $i <= 4; $i++)
					<div class="card">
						<h3>
							<div class="number">
								{{ $i }}
							</div>

							<div class="pip @if ($i >= 1) --filled @endif"></div>
							<div class="pip @if ($i >= 2) --filled @endif"></div>
							<div class="pip @if ($i >= 3) --filled @endif"></div>
							<div class="pip @if ($i >= 4) --filled @endif"></div>
						</h3>

						<div class="full-buy">
							$&nbsp;{{ nf($prices["{$abbr}_full"] - $initialBonus - $i * $bonusIncrement) }}
						</div>

						<div class="desperate">
							$&nbsp;{{ nf($prices["{$abbr}_desperate"] - $initialBonus - $i * $bonusIncrement) }}
						</div>

						<div class="following-round">
							following:

							<strong>
								$&nbsp;{{ nf($prices["{$abbr}_full"] - $initialBonus - $i * $bonusIncrement - $initialBonus - min($i + 1, 4) * $bonusIncrement) }}
								({{ $initialBonus + $i * $bonusIncrement }}, {{ $initialBonus + min($i + 1, 4) * $bonusIncrement }})
							</strong>
						</div>
					</div>
				@endfor
			</div>
		</section>
	@endforeach
@endsection
