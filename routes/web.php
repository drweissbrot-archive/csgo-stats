<?php

Route::get('/', HomeController::class)
	->name('index');

Route::get('/ladder/{ladder}', 'LadderController@read')
	->name('ladder');

Route::get('/series/{series}', 'SeriesController@read')
	->name('series');

Route::get('/match/{match}', 'MatchController@read')
	->name('match');

Route::get('/match/{match}/team-round-performance', 'MatchController@teamRoundPerformance')
	->name('match.team-round-performance');

Route::get('/match/{match}/player-round-performance/{player}', 'MatchController@playerRoundPerformance')
	->name('match.player-round-performance');

Route::get('/demo/{match}', 'MatchController@downloadDemo')
	->name('demo');

Route::get('/status', StatusController::class)
	->name('status');

Route::get('/player/{player}', PlayerController::class)
	->name('player');

Route::get('/team/{team}', TeamController::class)
	->name('team');

Route::get('/loss-bonus-buys/m4a1', 'LossBonusBuysController@m4a1')
	->name('loss-bonus-buys.m4a1');

Route::get('/loss-bonus-buys/m4a4', 'LossBonusBuysController@m4a4')
	->name('loss-bonus-buys.m4a4');

Route::get('/loss-bonus-buys/wingman/m4a1', 'LossBonusBuysController@wingman_m4a1')
	->name('loss-bonus-buys.wingman-m4a1');

Route::get('/loss-bonus-buys/wingman/m4a4', 'LossBonusBuysController@wingman_m4a4')
	->name('loss-bonus-buys.wingman-m4a4');
