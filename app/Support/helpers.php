<?php

use App\Player;

function nf($number, $decimals = 0, $plusSign = false)
{
	if (! is_numeric($number)) {
		return $number;
	}

	$formatted = number_format(
			$number,
			((float) (int) $number === (float) $number && $decimals < 1) ? 0 : $decimals,
			'.',
			','
		);

	if (! $plusSign || $number <= 0) {
		return $formatted;
	}

	return '+' . $formatted;
}

function otherTeam(string $team) : string
{
	if ($team === 'ct') {
		return 't';
	}

	if ($team === 't') {
		return 'ct';
	}

	if ($team === 'a') {
		return 'b';
	}

	if ($team === 'b') {
		return 'a';
	}

	if ($team === 'z') {
		return 'z';
	}
}

function teamNumberToAbbr($team) : string
{
	if ((string) $team === '3') {
		return 'ct';
	}

	if ((string) $team === '2') {
		return 't';
	}

	if ((string) $team === '1') {
		return 'spec';
	}

	if ((string) $team === '0') {
		return 'undef';
	}
}

function teamAbbrToNumber(string $team) : int
{
	if ($team === 'ct') {
		return 3;
	}

	if ($team === 't') {
		return 2;
	}

	if ($team === 'spec') {
		return 1;
	}

	if ($team === 'undef') {
		return 0;
	}
}

/**
 * @param int $roundNo round number, zero-indexed
 *
 * @return bool if the sides were swapped at the beginning of the given round
 */
function isSwapSideRound(int $roundNo) : bool
{
	return $roundNo === 15 || ($roundNo > 30 && ($roundNo - 3) % 6 === 0);
}

function divide($dividend, $divisor)
{
	if ($dividend === 0) {
		return 0;
	}

	if ($divisor === 0) {
		return \INF;
	}

	return $dividend / $divisor;
}

function unknownUser() : Player
{
	static $resolved;

	if (! $resolved) {
		$resolved = Player::where('steam_id', 'unknown_user')->firstOrFail();
	}

	return $resolved;
}
