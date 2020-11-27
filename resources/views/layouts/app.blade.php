<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		@hasSection ('document-title')
			@yield('document-title') –
		@else
			@hasSection ('page-title')
				@yield('page-title') –
			@endif
		@endif
		{{ config('app.name') }}
	</title>

	<link rel="stylesheet" href="{{ mix('css/app.css') }}">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap">
</head>
<body class="route-{{ Str::slug(str_replace('.', '-', optional(Route::current())->getName())) }}">
	<header>
		<a class="logo" href="{{ route('index') }}">
			CS:GO Stats
		</a>

		<nav>
			<ul>
				@foreach (app('all_ladders') as $ladder)
					<li>
						<a href="{{ route('ladder', $ladder->id) }}">
							{{ $ladder->name }}
						</a>
					</li>
				@endforeach

				<li>
					<a href="{{ route('status') }}">
						Status
					</a>
				</li>

				<li>
					<a href="{{ route('loss-bonus-buys.m4a1') }}">
						Buys
					</a>
				</li>
			</ul>
		</nav>
	</header>

	<main id="vue">
		@hasSection ('pre-title')
			<p class="pre-title">
				@yield('pre-title')
			</p>
		@endif

		@hasSection ('page-title')
			<h1 class="@yield('page-title-classes')">
				@yield('page-title')
			</h1>
		@endif

		@yield('content')
	</main>

	<script src="{{ mix('js/app.js') }}" async defer></script>
</body>
</html>
