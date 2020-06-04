<template>
	<div class="big-scoreboard">
		<div class="filter">
			<template v-if="teamRoundPerformanceRoute">
				<a :href="teamRoundPerformanceRoute">Team Round Performance</a> –
			</template>

			show stats for

			<template v-if="hasOvertime">
				<a href="#" @click.prevent="sumInclude('*')">Entire {{ (isSeries) ? 'Series' : 'Match' }}</a>
				(<a href="#" @click.prevent="sumInclude('ct_both')">CT</a> |
				<a href="#" @click.prevent="sumInclude('t_both')">T</a>)
			</template>

			<a href="#" @click.prevent="sumInclude('both_regulation')">Regulation</a>
			(<a href="#" @click.prevent="sumInclude('ct_regulation')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_regulation')">T</a>)

			<a href="#" @click.prevent="sumInclude('both_pistol')">Pistol Rounds</a>
			(<a href="#" @click.prevent="sumInclude('ct_pistol')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_pistol')">T</a>)

			<template v-if="hasOvertime">
				<a href="#" @click.prevent="sumInclude('both_overtime')">Overtime</a>
				(<a href="#" @click.prevent="sumInclude('ct_overtime')">CT</a> |
				<a href="#" @click.prevent="sumInclude('t_overtime')">T</a>)
			</template>
		</div>

		<table>
			<thead>
				<scoreboard-headings v-model="order" :playerRoundPerformanceRoute="playerRoundPerformanceRoute" />
			</thead>

			<tbody>
				<template v-for="(team, letter) in teams">
					<tr v-for="(player, index) in team.players" :key="player.id">
						<td v-if="index === 0" class="team" :rowspan="team.players.length">
							<div :class="[ 'total-score', { 'text-green': team.winner, 'text-red': team.loser } ]">
								{{ team.score }}
							</div>

							<div class="name">
								<img v-if="team.flag" :src="`https://countryflags.io/${team.flag}/flat/64.png`" :alt="team.flag" :title="team.flag">

								{{ team.name }}
							</div>

							<div class="additional-scores">
								<img :src="icons[team.side_first_half]" :alt="team.side_first_half" class="side">

								<span class="score">
									<template v-if="showRoundPercentages">
										{{ nf(team.score_first_half * 100, 0) }}&nbsp;%
									</template>

									<template v-else>
										{{ team.score_first_half }}
									</template>
								</span>

								–

								<span class="score">
									<template v-if="showRoundPercentages">
										{{ nf(team.score_second_half * 100, 0) }}&nbsp;%
									</template>

									<template v-else>
										{{ team.score_second_half }}
									</template>
								</span>

								<img :src="icons[team.side_second_half]" :alt="team.side_second_half" class="side">

								<div v-if="hasOvertime" title="Team Score across all Overtimes">
									<template v-if="team.score_overtime_2 !== undefined">
										<span class="score">
											{{ nf(team.score_overtime * 100, 0) }}&nbsp;%
										</span>
										OT
										<span class="score">
											{{ nf(team.score_overtime_2 * 100, 0) }}&nbsp;%
										</span>
									</template>

									<template v-else>
										{{ team.score_overtime }} OT
									</template>
								</div>
							</div>
						</td>

						<td>
							<a :href="playerRoute.replace('%', player.id)" class="player-name">
								<img v-if="player.flag" :src="`https://countryflags.io/${player.flag}/flat/64.png`" :alt="player.flag" :title="player.flag" class="inline-flag">
								{{ player.display_name }}
							</a>
						</td>

						<td v-if="playerRoundPerformanceRoute" :title="`Round Performance Graph for ${player.display_name}`">
							<a :href="playerRoundPerformanceRoute.replace('%', player.id)">
								RP
							</a>
						</td>

						<td>{{ stats[player.id].enemy_kills }}</td>
						<td>{{ stats[player.id].enemy_assists }}</td>
						<td>{{ stats[player.id].deaths }}</td>
						<td>{{ nf(divide(stats[player.id].enemy_kills, stats[player.id].deaths)) }}</td>
						<td>{{ nf(stats[player.id].enemy_kills - stats[player.id].deaths, 0) }}</td>
						<td>{{ nf(divide(stats[player.id].enemy_damage, roundCount[letter]), 0) }}</td>
						<td>{{ stats[player.id].enemy_utility_damage }}</td>
						<td>
							{{
								nf(divide(stats[player.id].enemy_headshot_kills, stats[player.id].enemy_kills) * 100, 0)
							}}%
						</td>
						<td>
							{{ nf(divide(stats[player.id].enemy_trade_kills, stats[player.id].enemy_kills) * 100, 0) }}%
						</td>
						<td>
							{{ nf(divide(stats[player.id].deaths_traded, stats[player.id].deaths) * 100, 0) }}%
						</td>
						<td>{{ nf(divide(stats[player.id].kast_rounds, roundCount[letter]) * 100, 0) }}%</td>
						<td>{{ nf(divide(stats[player.id].enemy_kills, roundCount[letter]), 2) }}</td>
						<td>{{ nf(divide(stats[player.id].deaths, roundCount[letter]), 2) }}</td>
						<td>
							{{
								nf(divide(stats[player.id].time_alive_ms, stats[player.id].max_alive_time_ms) * 100, 0)
							}}%
						</td>
						<td>{{ stats[player.id]['0_kill_rounds'] }}</td>
						<td>{{ stats[player.id]['1_kill_rounds'] }}</td>
						<td>{{ stats[player.id]['2_kill_rounds'] }}</td>
						<td>{{ stats[player.id]['3_kill_rounds'] }}</td>
						<td>{{ stats[player.id]['4_kill_rounds'] }}</td>
						<td>{{ stats[player.id]['5_kill_rounds'] }}</td>

						<td>
							{{
								stats[player.id].one_vs_1_kills
									+ stats[player.id].one_vs_2_kills
									+ stats[player.id].one_vs_3_kills
									+ stats[player.id].one_vs_4_kills
									+ stats[player.id].one_vs_5_kills
							}}
						</td>

						<td v-if="stats[player.id].one_vs_1_scenarios > 0 || stats[player.id].one_vs_2_scenarios > 0 || stats[player.id].one_vs_3_scenarios > 0 || stats[player.id].one_vs_4_scenarios > 0 || stats[player.id].one_vs_5_scenarios > 0">
							{{
								nf(divide(
									stats[player.id].one_vs_1_wins
										+ stats[player.id].one_vs_2_wins
										+ stats[player.id].one_vs_3_wins
										+ stats[player.id].one_vs_4_wins
										+ stats[player.id].one_vs_5_wins,
									stats[player.id].one_vs_1_scenarios
										+ stats[player.id].one_vs_2_scenarios
										+ stats[player.id].one_vs_3_scenarios
										+ stats[player.id].one_vs_4_scenarios
										+ stats[player.id].one_vs_5_scenarios
								) * 100, 0)
							}}&nbsp;%
						</td>

						<td v-else>
							N/A
						</td>

						<td>{{ stats[player.id].enemy_flash_assists }}</td>
						<td>{{ stats[player.id].enemies_flashed }}</td>
						<td>{{ nf(stats[player.id].enemies_flashed_duration) }}</td>
						<td>{{ stats[player.id].plants }}</td>
						<td>{{ stats[player.id].defuses }}</td>

						<td>{{ stats[player.id].team_kills }}</td>
						<td>{{ stats[player.id].team_assists }}</td>
						<td>{{ stats[player.id].team_damage }}</td>
						<td>{{ stats[player.id].teammates_flashed }}</td>
						<td>{{ nf(stats[player.id].teammates_flashed_duration) }}</td>
						<td>{{ stats[player.id].team_flash_assists }}</td>
					</tr>

					<tr v-if="letter === 'a'">
						<round-graph v-if="rounds" :icons="icons" :rounds="rounds" />

						<template v-else>
							&nbsp;
						</template>
					</tr>
				</template>
			</tbody>

			<tfoot>
				<scoreboard-headings v-model="order" :playerRoundPerformanceRoute="playerRoundPerformanceRoute" />
			</tfoot>
		</table>
	</div>
</template>

<script>
import Headings from './Headings'
import MathHelpers from '../Mixins/MathHelpers'
import RoundGraph from './RoundGraph'

export default {
	mixins: [ MathHelpers ],

	props: [
		'icons', 'isSeries', 'playedRoundCounts', 'playerRoundPerformanceRoute', 'playerRoute', 'rounds', 'showRoundPercentages', 'teamRoundPerformanceRoute', 'teamsData',
	],

	components: {
		'scoreboard-headings': Headings,
		RoundGraph,
	},

	beforeMount() {
		this.sortPlayers('kast_rounds', false)
	},

	data() {
		const otRounds = this.playedRoundCounts.a.overtime.ct + this.playedRoundCounts.a.overtime.t
		const totalRounds = this.playedRoundCounts.a.regulation.ct + this.playedRoundCounts.a.regulation.t + otRounds

		return {
			includeInSum: [ 't_regulation', 'ct_regulation', 't_overtime', 'ct_overtime' ],
			teams: this.teamsData,
			order: [ 'kast_rounds', false ],
			hasOvertime: otRounds > 0,
			roundCount: {
				a: totalRounds,
				b: totalRounds,
			},
		}
	},

	methods: {
		sumStats(stats) {
			if (this.includeInSum.length < 2) return stats[this.includeInSum[0]]

			const sum = Object.assign({}, stats[this.includeInSum[0]])

			for (let i = 1; i < this.includeInSum.length; i++) {
				for (const key in stats[this.includeInSum[i]]) {
					sum[key] += stats[this.includeInSum[i]][key]
				}
			}

			return sum
		},

		sumInclude(alias) {
			if (alias === '*') {
				this.includeInSum = [ 't_regulation', 'ct_regulation', 't_overtime', 'ct_overtime' ]
			} else if (alias === 'ct_both') {
				this.includeInSum = [ 'ct_regulation', 'ct_overtime' ]
			} else if (alias === 't_both') {
				this.includeInSum = [ 't_regulation', 't_overtime' ]
			} else if (alias === 'both_regulation') {
				this.includeInSum = [ 't_regulation', 'ct_regulation' ]
			} else if (alias === 'ct_regulation') {
				this.includeInSum = [ 'ct_regulation' ]
			} else if (alias === 't_regulation') {
				this.includeInSum = [ 't_regulation' ]
			} else if (alias === 'both_pistol') {
				this.includeInSum = [ 't_pistol', 'ct_pistol' ]
			} else if (alias === 'ct_pistol') {
				this.includeInSum = [ 'ct_pistol' ]
			} else if (alias === 't_pistol') {
				this.includeInSum = [ 't_pistol' ]
			} else if (alias === 'both_overtime') {
				this.includeInSum = [ 't_overtime', 'ct_overtime' ]
			} else if (alias === 'ct_overtime') {
				this.includeInSum = [ 'ct_overtime' ]
			} else if (alias === 't_overtime') {
				this.includeInSum = [ 't_overtime' ]
			}

			this.sortPlayers(...this.order)
		},

		sortPlayers(criterion, ascending = true) {
			const higher = (ascending) ? 1 : -1
			const lower = (ascending) ? -1 : 1

			for (const letter in this.teams) {
				this.teams[letter].players.sort((a, b) => {
					if (a.bot && ! b.bot) return 1 // always put all bots to the end of the list

					a = this.criterion(criterion, a)
					b = this.criterion(criterion, b)

					if (a > b) return higher
					else if (a === b) return 0

					return lower
				})
			}
		},

		criterion(criterion, player) {
			if (criterion === 'kd_ratio') {
				return this.stats[player.id].enemy_kills / this.stats[player.id].deaths
			}

			if (criterion === 'kill_difference') {
				return this.stats[player.id].enemy_kills - this.stats[player.id].deaths
			}

			if (criterion === 'hspctg') {
				return this.stats[player.id].enemy_headshot_kills / this.stats[player.id].enemy_kills
			}

			if (criterion === 'enemy_trade_kills') {
				return this.stats[player.id].enemy_trade_kills / this.stats[player.id].enemy_kills
			}

			if (criterion === 'deaths_traded') {
				return this.stats[player.id].deaths_traded / this.stats[player.id].deaths
			}

			if (criterion === 'clutch_kills') {
				return this.stats[player.id].one_vs_1_kills + this.stats[player.id].one_vs_2_kills + this.stats[player.id].one_vs_3_kills + this.stats[player.id].one_vs_4_kills + this.stats[player.id].one_vs_5_kills
			}

			if (criterion === 'clutch_wins') {
				const scenarios = this.stats[player.id].one_vs_1_scenarios + this.stats[player.id].one_vs_2_scenarios + this.stats[player.id].one_vs_3_scenarios + this.stats[player.id].one_vs_4_scenarios + this.stats[player.id].one_vs_5_scenarios

				// always consider people who didn't get into any clutch situations lower than those who did, but didn't win any
				if (scenarios === 0) return -1

				return this.divide(
					this.stats[player.id].one_vs_1_wins + this.stats[player.id].one_vs_2_wins + this.stats[player.id].one_vs_3_wins + this.stats[player.id].one_vs_4_wins + this.stats[player.id].one_vs_5_wins,
					scenarios
				)
			}

			return this.stats[player.id][criterion]
		},

		teamNumberToAbbr(number) {
			if (number === 2) return 't'
			if (number === 3) return 'ct'

			throw false
		},
	},

	computed: {
		stats() {
			const players = {}

			for (const team of Object.values(this.teams)) {
				for (const player of team.players) {
					players[player.id] = this.sumStats(player.stats)
				}
			}

			return players
		},
	},

	watch: {
		includeInSum(phases) {
			let a = 0
			let b = 0

			for (const phase of phases) {
				const [side, part] = phase.split('_')

				a += this.playedRoundCounts.a[part][side]
				b += this.playedRoundCounts.b[part][side]
			}

			this.roundCount.a = a
			this.roundCount.b = b
		},

		order([ criterion, ascending ]) {
			this.sortPlayers(criterion, ascending)
		},
	},
}
</script>
