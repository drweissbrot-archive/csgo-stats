<template>
	<tr>
		<th></th>
		<th></th>

		<th v-if="playerRoundPerformanceRoute"></th>

		<sth v-for="{ criterion, title, heading } in headings"
			:order="value"
			@input="$emit('input', $event)"
			:criterion="criterion"
			:title="title"
			:key="title"
		>
			{{ heading }}
		</sth>
	</tr>
</template>

<script>
import SortableTh from '../SortableTh'

export default {
	props: [ 'playerRoundPerformanceRoute', 'value' ],

	components: {
		'sth': SortableTh,
	},

	data() {
		return {
			headings: [
				{ criterion: 'enemy_kills', title: 'Kills', heading: 'K' },
				{ criterion: 'enemy_assists', title: 'Assists', heading: 'A' },
				{ criterion: 'deaths', title: 'Deaths', heading: 'D' },
				{ criterion: 'kd_ratio', title: 'Kill/Death Ratio', heading: 'K/D' },
				{ criterion: 'kill_difference', title: 'Kill-Death Difference', heading: 'K-D' },
				{ criterion: 'enemy_damage', title: 'Average Damage per Round', heading: 'ADR' },
				{ criterion: 'enemy_utility_damage', title: 'Utility Damage', heading: 'UD' },
				{ criterion: 'hspctg', title: 'Percentage of kills which were headshots', heading: 'HS' },
				{ criterion: 'enemy_trade_kills', title: 'Percentage of kills that traded a teammate', heading: 'TRD' },
				{ criterion: 'deaths_traded', title: 'Percentage of deaths that were traded', heading: 'DT' },
				{
					criterion: 'kast_rounds',
					title: 'Percentage of rounds in which the player either had a kill, an assist, survived or was traded',
					heading: 'KAST',
				},
				{ criterion: 'enemy_kills', title: 'Average Kills per Round', heading: 'KPR' },
				{ criterion: 'deaths', title: 'Average Deaths per Round', heading: 'DPR' },
				{ criterion: 'time_alive_ms', title: 'Average time per round the player was alive', heading: 'ALV' },
				{ criterion: '0_kill_rounds', title: 'Rounds in which the player had 0 kills', heading: '0K' },
				{ criterion: '1_kill_rounds', title: 'Rounds in which the player had 1 kill', heading: '1K' },
				{ criterion: '2_kill_rounds', title: 'Rounds in which the player had 2 kills', heading: '2K' },
				{ criterion: '3_kill_rounds', title: 'Rounds in which the player had 3 kills', heading: '3K' },
				{ criterion: '4_kill_rounds', title: 'Rounds in which the player had 4 kills', heading: '4K' },
				{
					criterion: '5_kill_rounds',
					title: 'Rounds in which the player had 5 (or more) kills',
					heading: '5K',
				},
				{ criterion: 'clutch_kills', title: 'Kills the player made while in a 1vX situation', heading: 'CK' },
				{ criterion: 'clutch_wins', title: '1vX situations the player won', heading: 'CW' },
				{ criterion: 'enemy_flash_assists', title: 'Flash Assists', heading: 'FA' },
				{ criterion: 'enemies_flashed', title: 'Enemies Flashed', heading: 'EF' },
				{
					criterion: 'enemies_flashed_duration',
					title: 'Total duration that enemies were blinded for from flashbangs thrown by the player',
					heading: 'FD',
				},
				{ criterion: 'plants', title: 'Plants', heading: 'P' },
				{ criterion: 'defuses', title: 'Defuses', heading: 'D' },

				{ criterion: 'team_kills', title: 'Team Kills', heading: 'TK' },
				{ criterion: 'team_assists', title: 'Team Assists', heading: 'TA' },
				{ criterion: 'team_damage', title: 'Team Damage', heading: 'TD' },
				{ criterion: 'teammates_flashed', title: 'Teammates Flashed', heading: 'TF' },
				{
					criterion: 'teammates_flashed_duration',
					title: 'Total duration that teammates were blinded for from flashbangs thrown by the player',
					heading: 'TFD',
				},
				{ criterion: 'team_flash_assists', title: 'Team Flash Assists', heading: 'TFA' },
			],
		}
	},
}
</script>
