<div>
	<img class="weapon"
		src="{{ mix("images/{$event->get('weapon')}.svg") }}"
		alt="{{ $event->get('weapon') }}"
		title="{{ $event->get('weapon') }}"
	>
	@if ($event->get('headshot'))
		<img class="weapon" src="{{ mix('images/icon_headshot.svg') }}" alt="Headshot">
	@endif
	@if ($event->get('through_wall'))
		<img class="weapon" src="{{ mix('images/penetrate.svg') }}" alt="through Wall or other Player">
	@endif
	@if ($event->get('through_smoke'))
		<img class="weapon" src="{{ mix('images/smoke_kill.svg') }}" alt="through Smoke">
	@endif
	@if ($event->get('noscope'))
		<img class="weapon" src="{{ mix('images/noscope.svg') }}" alt="Noscope">
	@endif
</div>
