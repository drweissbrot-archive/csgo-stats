<template>
	<tr>
		<th></th>

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
	props: [ 'value' ],

	components: {
		'sth': SortableTh,
	},

	data() {
		return {
			headings: [
				{ criterion: 'matches_played', title: 'Matches Played', heading: 'P' },
				{ criterion: 'matches_won', title: 'Matches Won', heading: 'W' },
				{ criterion: 'match_win_pctg', title: 'Match Win Rate', heading: '%' },
				{ criterion: 'matches_tied', title: 'Matches Tied', heading: 'T' },
				{ criterion: 'matches_lost', title: 'Matches Lost', heading: 'L' },
				{ criterion: 'ard', title: 'Average Round Difference', heading: 'ARD' },
				{ criterion: 'rounds_played', title: 'Rounds Played', heading: 'RP' },
				{ criterion: 'rounds_won', title: 'Rounds Won', heading: 'RW' },
				{ criterion: 'round_win_pctg', title: 'Round Win Percentage', heading: '%' },
				{ criterion: 'enemy_kills', title: 'Kills', heading: 'K' },
				{ criterion: 'kpr', title: 'Kills per Round', heading: 'KPR' },
				{ criterion: 'enemy_assists', title: 'Assists', heading: 'A' },
				{ criterion: 'apr', title: 'Assists per Round', heading: 'APR' },
				{ criterion: 'deaths', title: 'Deaths', heading: 'D' },
				{ criterion: 'dpr', title: 'Deaths per Round', heading: 'DPR' },
				{ criterion: 'kd_ratio', title: 'Kill/Death Ratio', heading: 'K/D' },
				{ criterion: 'kill_difference', title: 'Kill-Death Difference', heading: 'K-D' },
				{ criterion: 'adr', title: 'Average Damage per Round', heading: 'ADR' },
				{ criterion: 'udr', title: 'Average Utility Damage per Round', heading: 'UDR' },
				{ criterion: 'hspctg', title: 'Percentage of kills which were headshots', heading: 'HS%' },
				{ criterion: 'kast_pctg', title: 'Percentage of rounds in which the player either had a kill, an assist, survived or was traded', heading: 'KAST%' },
				{ criterion: 'time_alive_pctg', title: 'Average time per round the player was alive', heading: 'TA%' },
				{ criterion: '0k_pctg', title: 'Percentage of rounds in which the player had 0 kills', heading: '0K%' },
				{ criterion: '1k_pctg', title: 'Percentage of rounds in which the player had 1 kill', heading: '1K%' },
				{ criterion: '2k_pctg', title: 'Percentage of rounds in which the player had 2 kills', heading: '2K%' },
				{ criterion: '3k_pctg', title: 'Percentage of rounds in which the player had 3 kills', heading: '3K%' },
				{ criterion: '4k_pctg', title: 'Percentage of rounds in which the player had 4 kills', heading: '4K%' },
				{ criterion: '5k_pctg', title: 'Percentage of rounds in which the player had 5 kills', heading: '5K%' },
				{
					criterion: '5_kill_rounds',
					title: 'Total number of rounds in which the player had 5 kills',
					heading: 'Aces',
				},
				{ criterion: 'clutch_scenarios', title: 'Total number of 1vX situations', heading: 'CR' },
				{ criterion: 'clutch_wins', title: 'Total number of 1vX round wins', heading: 'CW' },
				{ criterion: 'clutch_win_pctg', title: 'Clutch Win Rate', heading: '%' },
				{ criterion: 'clutch_kills', title: 'Total number of kills in 1vX situations', heading: 'CK' },
			],
		}
	},
}
</script>
