<?php

namespace App\Providers;

use App\Ladder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	public function register()
	{
		//
	}

	public function boot()
	{
		$macros = [
			Collection::class => [
				'addNum' => function ($key, $number) {
					return $this->put($key, $this->get($key) + $number);
				},

				'recursive' => function () {
					return $this->map(function ($value) {
						if (is_object($value) || is_array($value)) {
							return collect($value)->recursive();
						}

						return $value;
					});
				},
			],
		];

		foreach ($macros as $class => $definitions) {
			foreach ($definitions as $name => $handler) {
				$class::macro($name, $handler);
			}
		}

		$this->app->singleton('all_ladders', function () {
			return Ladder::orderBy('name')->get();
		});
	}
}
