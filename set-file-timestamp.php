<?php

// You can use this to more easily upload matches from this tool to Leetify.
// Get the demo files, unzip them, place them in the __timestamp directory
// (relative to this project's root), and make sure they're named
// "<id-from-this-tools-database>.dem". Then, cd to this project's root and run
// "php artisan tinker set-file-timestamp.php". The file timestamps should now
// be updated to the end time of the match, which should make Leetify get the
// proper timestamps.

$files = collect(glob(__DIR__ . '/__timestamp/*.dem'))->keyBy(fn ($file) => explode('.', basename($file), 2)[0]);

$matches = CsMatch::whereIn('id', $files->keys())->get()->keyBy('id');

if (count($matches) !== count($files)) {
	echo 'COUNT MISMATCH ' . count($matches) . ' ' . count($files) . "\n";
	die();
}

foreach ($files as $id => $file) {
	if (! $matches->has($id)) {
		die("${id} MISSING!\n");
	}

	$finishedAt = $matches->get($id)->started_at->getTimestamp() + $matches->get($id)->duration;

	touch($file, $finishedAt, $finishedAt);
}

exit;
