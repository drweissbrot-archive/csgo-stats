@extends('layouts.app')

@section('page-title')
	Latest Series
@endsection

@section('content')
	@foreach ($latestSeries as $series)
		@include('partials.series', compact('series'))
	@endforeach
@endsection
