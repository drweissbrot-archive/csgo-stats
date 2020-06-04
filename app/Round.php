<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Str;

class Round extends Model
{
	public const EVENT_TYPES = [
		BotTakeoverEvent::class => 'botTakeoverEvents',
		DamageEvent::class => 'damageEvents',
		DefuseEvent::class => 'defuseEvents',
		ExplodedEvent::class => 'explodedEvents',
		FlashDetonatedEvent::class => 'flashbangDetonatedEvents',
		FlashThrownEvent::class => 'flashbangThrownEvents',
		FlashedEvent::class => 'flashedEvents',
		FreezeTimeEndedEvent::class => 'freezeTimeEndedEvents',
		HeDetonatedEvent::class => 'heDetonatedEvents',
		HeThrownEvent::class => 'heThrownEvents',
		ItemPickupEvent::class => 'itemPickupEvents',
		KillEvent::class => 'killEvents',
		MolotovDetonatedEvent::class => 'molotovDetonatedEvents',
		MolotovThrownEvent::class => 'molotovThrownEvents',
		MoneyEquipmentEvent::class => 'moneyEquipmentEvents',
		MvpEvent::class => 'mvpEvents',
		PlantEvent::class => 'plantEvents',
		RoundStartEvent::class => 'roundStartEvents',
		RoundWinnerEvent::class => 'roundWinnerEvents',
		SmokeDetonatedEvent::class => 'smokeDetonatedEvents',
		SmokeThrownEvent::class => 'smokeThrownEvents',
	];

	protected $guarded = [];

	protected $cachedEvents;

	public function scopeOnlyCounted(Builder $rounds)
	{
		$rounds->where('is_counted', true);
	}

	public function botTakeoverEvents()
	{
		return $this->hasMany(BotTakeoverEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function damageEvents()
	{
		return $this->hasMany(DamageEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function defuseEvents()
	{
		return $this->hasMany(DefuseEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function explodedEvents()
	{
		return $this->hasMany(ExplodedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function flashbangDetonatedEvents()
	{
		return $this->hasMany(FlashbangDetonatedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function flashbangThrownEvents()
	{
		return $this->hasMany(FlashbangThrownEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function flashedEvents()
	{
		return $this->hasMany(FlashedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function freezeTimeEndedEvents()
	{
		return $this->hasMany(FreezeTimeEndedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function heDetonatedEvents()
	{
		return $this->hasMany(HeDetonatedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function heThrownEvents()
	{
		return $this->hasMany(HeThrownEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function itemPickupEvents()
	{
		return $this->hasMany(ItemPickupEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function killEvents()
	{
		return $this->hasMany(KillEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function molotovDetonatedEvents()
	{
		return $this->hasMany(MolotovDetonatedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function molotovThrownEvents()
	{
		return $this->hasMany(MolotovThrownEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function moneyEquipmentEvents()
	{
		return $this->hasMany(MoneyEquipmentEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function mvpEvents()
	{
		return $this->hasMany(MvpEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function plantEvents()
	{
		return $this->hasMany(PlantEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function roundStartEvents()
	{
		return $this->hasMany(RoundStartEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function roundWinnerEvents()
	{
		return $this->hasMany(RoundWinnerEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function smokeDetonatedEvents()
	{
		return $this->hasMany(SmokeDetonatedEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function smokeThrownEvents()
	{
		return $this->hasMany(SmokeThrownEvent::class)->orderBy('index_within_round', 'ASC');
	}

	public function getEventsAttribute()
	{
		if ($this->cachedEvents === null) {
			$this->loadMissing(array_values(static::EVENT_TYPES));

			$events = collect();

			foreach (static::EVENT_TYPES as $relation) {
				$events = $events->merge($this->{$relation});
			}

			$this->cachedEvents = $events->sortBy('index_within_round')->values();
		}

		return $this->cachedEvents;
	}

	public function setTeamZSurvivedAttribute($value)
	{
		// just don't do anything
	}

	/**
	 * @param Collection $data         the event data
	 * @param Collection $players      all players in the game, steamId => databaseId
	 * @param int        $i            event number $i within round
	 * @param string     $winnerTeamId ayy
	 */

	/**
	 * @param Collection $data         the event data
	 * @param Collection $players      all players in the game, steamId => databaseId
	 * @param int        $i            the event is number $i within round (zero-indexed)
	 * @param string     $winnerTeamId the database id of the round winner
	 */
	public function addEvent(Collection $data, Collection $demo, Collection $players, int $i, $winnerTeamId)
	{
		$relation = Str::camel($data->pull('type') . '_event');
		$type = '\App\\' . Str::ucfirst($relation);

		$this->{$relation . 's'}()->save($type::makeFromData($data, $demo, $players, $i, $winnerTeamId));
	}
}
