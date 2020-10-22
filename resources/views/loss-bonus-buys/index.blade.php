@extends('layouts.app')

@section('page-title')
	Loss Bonus Buys
@endsection

@section('content')
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
					</div>
				@endfor
			</div>
		</section>
	@endforeach
@endsection
