<?php

return [
	/*
	 * Default Filesystem Disk
	 *
	 * Here you may specify the default filesystem disk that should be used
	 * by the framework. The "local" disk, as well as a variety of cloud
	 * based disks are available to your application. Just store away!
	 */
	'default' => env('FILESYSTEM_DRIVER', 'local'),

	/*
	 * Default Cloud Filesystem Disk
	 *
	 * Many applications store files both locally and in the cloud. For this
	 * reason, you may specify a default "cloud" driver here. This driver
	 * will be bound as the Cloud disk implementation in the container.
	 */
	'cloud' => env('FILESYSTEM_CLOUD', 's3'),

	/*
	 * Filesystem Disks
	 *
	 * Here you may configure as many filesystem "disks" as you wish, and you
	 * may even configure multiple disks of the same driver. Defaults have
	 * been setup for each driver as an example of the required options.
	 *
	 * Supported Drivers: "local", "ftp", "sftp", "s3"
	 */
	'disks' => [
		'local' => [
			'driver' => 'local',
			'root' => storage_path('app'),
		],

		'tmp' => [
			'driver' => 'local',
			'root' => storage_path('app/tmp'),
		],

		'demos' => [
			'driver' => 's3',
			'bucket' => env('DEMOS_BUCKET'),
			'endpoint' => env('DEMOS_ENDPOINT'),
			'key' => env('DEMOS_ACCESS_KEY_ID'),
			'region' => env('DEMOS_DEFAULT_REGION', 'any'),
			'secret' => env('DEMOS_SECRET_ACCESS_KEY'),
			'url' => env('DEMOS_URL'),
			'use_path_style_endpoint' => env('DEMOS_USE_PATH_STYLE_ENDPOINT', false),
		],

		'demo_intake' => [
			'driver' => 's3',
			'bucket' => env('INTAKE_BUCKET'),
			'endpoint' => env('INTAKE_ENDPOINT'),
			'key' => env('INTAKE_ACCESS_KEY_ID'),
			'region' => env('INTAKE_DEFAULT_REGION', 'any'),
			'secret' => env('INTAKE_SECRET_ACCESS_KEY'),
			'url' => env('INTAKE_URL'),
			'use_path_style_endpoint' => env('INTAKE_USE_PATH_STYLE_ENDPOINT', false),
		],

		'public' => [
			'driver' => 'local',
			'root' => storage_path('app/public'),
			'url' => env('APP_URL') . '/storage',
			'visibility' => 'public',
		],

		's3' => [
			'driver' => 's3',
			'key' => env('AWS_ACCESS_KEY_ID'),
			'secret' => env('AWS_SECRET_ACCESS_KEY'),
			'region' => env('AWS_DEFAULT_REGION'),
			'bucket' => env('AWS_BUCKET'),
			'url' => env('AWS_URL'),
			'endpoint' => env('AWS_ENDPOINT'),
		],
	],

	/*
	 * Symbolic Links
	 *
	 * Here you may configure the symbolic links that will be created when the
	 * `storage:link` Artisan command is executed. The array keys should be
	 * the locations of the links and the values should be their targets.
	 */
	'links' => [
		public_path('storage') => storage_path('app/public'),
	],
];
