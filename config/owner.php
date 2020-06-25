<?php

return [
	// NB! All steam ids provided will be replaced with the first provided steam id.
	// e.g. steam_ids => 'a,b,c' --> every event for players b or c will be attributed to player a
	'steam_ids' => env('OWNER_STEAM_IDS'),
	'flag' => env('OWNER_STEAM_FLAG'),
	'teams' => env('OWNER_TEAM_NAMES'),
];
