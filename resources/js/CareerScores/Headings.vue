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
	props: [ 'rowsAre', 'value' ],

	components: {
		'sth': SortableTh,
	},

	data() {
		const headings = (this.rowsAre === 'maps')
			? [
				{ criterion: 'matches_played', title: 'Matches Played', heading: 'P' },
				{ criterion: 'matches_won', title: 'Matches Won', heading: 'W' },
				{ criterion: 'match_win_pctg', title: 'Match Win Rate', heading: '%' },
				{ criterion: 'matches_tied', title: 'Matches Tied', heading: 'T' },
				{ criterion: 'matches_lost', title: 'Matches Lost', heading: 'L' },
				{ criterion: 'ard', title: 'Average Round Difference', heading: 'ARD' },
				{ criterion: 'rounds_played', title: 'Rounds Played', heading: 'RP' },
				{ criterion: 'rounds_won', title: 'Rounds Won', heading: 'RW' },
				{ criterion: 'round_win_pctg', title: 'Round Win Percentage', heading: '%' },
			]
			: []

		headings.push({ criterion: 'enemy_kills', title: 'Kills', heading: 'K' },)
		headings.push({ criterion: 'kpr', title: 'Kills per Round', heading: 'KPR' },)
		headings.push({ criterion: 'enemy_assists', title: 'Assists', heading: 'A' },)
		headings.push({ criterion: 'apr', title: 'Assists per Round', heading: 'APR' },)
		headings.push({ criterion: 'deaths', title: 'Deaths', heading: 'D' },)
		headings.push({ criterion: 'dpr', title: 'Deaths per Round', heading: 'DPR' },)
		headings.push({ criterion: 'kd_ratio', title: 'Kill/Death Ratio', heading: 'K/D' },)
		headings.push({ criterion: 'kill_difference', title: 'Kill-Death Difference', heading: 'K-D' },)
		headings.push({ criterion: 'adr', title: 'Average Damage per Round', heading: 'ADR' },)
		headings.push({ criterion: 'udr', title: 'Average Utility Damage per Round', heading: 'UDR' },)
		headings.push({ criterion: 'hspctg', title: 'Percentage of kills which were headshots', heading: 'HS%' },)

		if (this.rowsAre === 'players') {
			headings.push({
				criterion: 'trade_kills_pctg',
				title: 'Percentage of kills that traded a teammate',
				heading: 'TRD',
			})

			headings.push({
				criterion: 'deaths_traded_pctg',
				title: 'Percentage of deaths that were traded',
				heading: 'DT',
			})
		}

		headings.push({
			criterion: 'kast_pctg',
			title: 'Percentage of rounds in which the player either had a kill, an assist, survived or was traded',
			heading: 'KAST%',
		})

		headings.push({
			criterion: 'time_alive_pctg',
			title: 'Average time per round the player was alive',
			heading: 'TA%',
		})

		headings.push({
			criterion: '0k_pctg',
			title: 'Percentage of rounds in which the player had 0 kills',
			heading: '0K%',
		})

		headings.push({
			criterion: '1k_pctg',
			title: 'Percentage of rounds in which the player had 1 kill',
			heading: '1K%',
		})

		headings.push({
			criterion: '2k_pctg',
			title: 'Percentage of rounds in which the player had 2 kills',
			heading: '2K%',
		})

		headings.push({
			criterion: '3k_pctg',
			title: 'Percentage of rounds in which the player had 3 kills',
			heading: '3K%',
		})

		headings.push({
			criterion: '4k_pctg',
			title: 'Percentage of rounds in which the player had 4 kills',
			heading: '4K%',
		})

		headings.push({
			criterion: '5k_pctg',
			title: 'Percentage of rounds in which the player had 5 kills',
			heading: '5K%',
		})

		headings.push({
			criterion: '5_kill_rounds',
			title: 'Total number of rounds in which the player had 5 kills',
			heading: 'Aces',
		})

		headings.push({ criterion: 'clutch_scenarios', title: 'Total number of 1vX situations', heading: 'CR' })
		headings.push({ criterion: 'clutch_wins', title: 'Total number of 1vX round wins', heading: 'CW' })
		headings.push({ criterion: 'clutch_win_pctg', title: 'Clutch Win Rate', heading: '%' })
		headings.push({ criterion: 'clutch_kills', title: 'Total number of kills in 1vX situations', heading: 'CK' })

		if (this.rowsAre === 'players') {
			headings.push({ criterion: 'enemies_flashed_per_round', title: 'Enemies Flashed per Round', heading: 'EFR' })

			headings.push({
				criterion: 'avg_enemy_flashed_duration',
				title: 'Average duration a flashed enemy was flashed for',
				heading: 'AFD',
			})

			headings.push({
				criterion: 'enemy_flashed_duration_per_round',
				title: 'Average duration enemies were flashed for per round from flashbangs thrown by this player',
				heading: 'FDR',
			})
		}

		return { headings }
	},
}
</script>
