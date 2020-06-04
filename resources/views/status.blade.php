@extends('layouts.app')

@section('page-title')
	Status
@endsection

@section('content')
	<section>
		<h2>Latest Series</h2>

		<table class="small">
			<tbody>
				@foreach ($ladders as $ladder)
					<tr>
						<td>
							<a href="{{ route('ladder', $ladder->id) }}">
								{{ $ladder->name }}
							</a>
						</td>

						<td>
							@if ($ladder->series->isEmpty())
								no series
							@else
								<a href="{{ route('series', $ladder->series->first()->id) }}">
									{{ $ladder->series->first()->matches->first()->started_at }}

									@if ($ladder->series->first()->name)
										({{ $ladder->series->first()->name }})
									@endif
								</a>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</section>

	<section>
		<h2>
			Matches with Flawed Data
		</h2>

		@forelse ($matchesWithRoundMismatch as $match)
			<p>
				<a href="{{ route('match', $match->id) }}">
					{{ $match->series->ladder->name }}
					{{ $match->map->display_name }}
					{{ $match->started_at }}

					(uncounted rounds: {{ $match->rounds->pluck('round_no')->join(', ') }})
				</a>
			</p>
		@empty
			<p>
				N/A
			</p>
		@endforelse
	</section>
@endsection
