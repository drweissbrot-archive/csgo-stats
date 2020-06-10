<template>
	<div v-if="loaded" class="career-scores">
		<div class="cards">
			<div class="card" v-if="steamData">
				<div class="title">
					Steam
				</div>

				<a :href="steamData.url" class="value">
					{{ steamData.name }}
				</a>
			</div>

			<div class="card" title="Total Number of Series Played – NB! this number ignores filters for maps or ladders">
				<div class="title">
					Series Played
				</div>

				<div class="value">
					{{ seriesPlayed }}
				</div>
			</div>

			<div class="card">
				<div class="title">
					Matches Played
				</div>

				<div class="value">
					{{ n(firstRow.matches_played) / this.includedPhases.length }}
				</div>
			</div>

			<div class="card">
				<div class="title">
					Matches Won
				</div>

				<div class="value">
					{{ n(firstRow.matches_won) / this.includedPhases.length }}
					({{ nf(divide(n(firstRow.matches_won), n(firstRow.matches_played)) * 100, 0, 2) }}&nbsp;%)
				</div>
			</div>

			<div class="card">
				<div class="title">
					Matches Tied
				</div>

				<div class="value">
					{{ n(firstRow.matches_tied) / this.includedPhases.length }}
					({{ nf(divide(n(firstRow.matches_tied), n(firstRow.matches_played)) * 100, 0, 2) }}&nbsp;%)
				</div>
			</div>

			<div class="card">
				<div class="title">
					Matches Lost
				</div>

				<div class="value">
					{{ n(firstRow.matches_lost) / this.includedPhases.length }}
					({{ nf(divide(n(firstRow.matches_lost), n(firstRow.matches_played)) * 100, 0, 2) }}&nbsp;%)
				</div>
			</div>

			<div class="card" title="Average Round Difference">
				<div class="title">
					ARD
				</div>

				<div class="value">
					{{ nf(divide(n(firstRow.round_difference), n(firstRow.matches_played)), 2) }}
				</div>
			</div>

			<div class="card">
				<div class="title">
					Rounds Played
				</div>

				<div class="value">
					{{ n(firstRow.rounds_played) }}
				</div>
			</div>

			<div class="card">
				<div class="title">
					Rounds Won
				</div>

				<div class="value">
					{{ n(firstRow.rounds_won) }}
					({{ nf(divide(n(firstRow.rounds_won), n(firstRow.rounds_played)) * 100, 0, 2) }}&nbsp;%)
				</div>
			</div>
		</div>

		<div class="included-map-images" title="Maps included in stats – Ctrl+Click to invert selection">
			<a href="#"
				v-for="({ filename, display_name }, id) in allMaps"
				:class="[ 'map', '--hero', `--${filename}`, { '--active': includedMaps.includes(id) } ]"
				@click.prevent="toggleIncludedMap($event, id)"
				:style="{ 'max-width': (100 / Object.keys(allMaps).length) + '%' }"
			>
			<div class="inner">
				<div class="name text-ellipsis" :title="display_name">
					{{ display_name }}
				</div>
			</div>
			</a>
		</div>

		<div class="filter">
			Ladders:
			<template v-for="ladder in allLadders">
				<label>
					<input type="checkbox"
						:checked="includedLadders.includes(ladder.id)"
						@change.prevent="toggleIncludedLadder(ladder.id)"
					>
					{{ ladder.name }}
				</label>
			</template>

			– show stats for

			<a href="#" @click.prevent="sumInclude('*')">all Rounds</a>
			(<a href="#" @click.prevent="sumInclude('ct_both')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_both')">T</a>)

			<a href="#" @click.prevent="sumInclude('both_regulation')">Regulation</a>
			(<a href="#" @click.prevent="sumInclude('ct_regulation')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_regulation')">T</a>)

			<a href="#" @click.prevent="sumInclude('both_pistol')">Pistol Rounds</a>
			(<a href="#" @click.prevent="sumInclude('ct_pistol')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_pistol')">T</a>)

			<a href="#" @click.prevent="sumInclude('both_overtime')">Overtime</a>
			(<a href="#" @click.prevent="sumInclude('ct_overtime')">CT</a> |
			<a href="#" @click.prevent="sumInclude('t_overtime')">T</a>)
		</div>

		<section>
			<h2>Scoreboard</h2>

			<table>
				<thead>
					<headings v-model="order" :rowsAre="rowsAre" />
				</thead>

				<tbody>
					<scores-row v-if="rowsAre === 'players' || (map === 'total' || allMaps.hasOwnProperty(map))"
						v-for="map in includedMapsSorted" :key="map"
						:map="(rowsAre === 'players')
							? allPlayers[map]
							: ((map === 'total') ? 'All Maps' : allMaps[map].display_name)"
						:stats="stats[map]"
						:includedPhases="includedPhases.length"
						:rowsAre="rowsAre"
					/>
				</tbody>
			</table>
		</section>

		<section>
			<h2>Weapons</h2>

			<weapon-kills-chart
				:weaponKills="weaponKills"
				:totalKills="
					(stats.hasOwnProperty('total'))
						? stats.total.enemy_kills
						: weaponKills.reduce((sum, [ _, k ]) => sum += k, 0)
				"
			/>
		</section>
	</div>
</template>

<script>
import { get, set } from 'idb-keyval'
import Headings from './Headings'
import MathHelpers from '../Mixins/MathHelpers'
import ScoresRow from './ScoresRow'
import WeaponKillsChart from './WeaponKillsChart'

let initialIncludedLadders, initialIncludedMaps

export default {
	mixins: [ MathHelpers ],

	props: [ 'allLadders', 'allMaps', 'allPlayers', 'allStats', 'rowsAre', 'seriesPlayed', 'steamData' ],

	components: {
		Headings,
		ScoresRow,
		WeaponKillsChart,
	},

	async mounted() {
		this.includedLadders = await get('included_ladders') || this.allLadders.map(({ id }) => id)
		this.includedMaps = await get('included_maps') || Object.keys(this.allMaps)
		this.loaded = true
	},

	data() {
		return {
			loaded: false,
			includedLadders: [],
			includedMaps: [],
			includedPhases: [ 't_regulation', 'ct_regulation', 't_overtime', 'ct_overtime' ],

			order: (this.rowsAre === 'players')
				? [ 'kast_pctg', false ]
				: [ 'match_win_pctg', false ],
		}
	},

	methods: {
		toggleIncludedMap(e, name) {
			if (! e.ctrlKey) {
				const index = this.includedMaps.indexOf(name)

				return (index === -1)
					? this.includedMaps.push(name)
					: this.includedMaps.splice(index, 1)
			}

			for (const id in this.allMaps) {
				const index = this.includedMaps.indexOf(id)
				;(index === -1)
					? this.includedMaps.push(id)
					: this.includedMaps.splice(index, 1)
			}
		},

		toggleIncludedLadder(ladder) {
			const index = this.includedLadders.indexOf(ladder)
			;(index === -1)
				? this.includedLadders.push(ladder)
				: this.includedLadders.splice(index, 1)
		},

		sumInclude(alias) {
			if (alias === '*') {
				this.includedPhases = [ 't_regulation', 'ct_regulation', 't_overtime', 'ct_overtime' ]
			} else if (alias === 'ct_both') {
				this.includedPhases = [ 'ct_regulation', 'ct_overtime' ]
			} else if (alias === 't_both') {
				this.includedPhases = [ 't_regulation', 't_overtime' ]
			} else if (alias === 'both_regulation') {
				this.includedPhases = [ 't_regulation', 'ct_regulation' ]
			} else if (alias === 'ct_regulation') {
				this.includedPhases = [ 'ct_regulation' ]
			} else if (alias === 't_regulation') {
				this.includedPhases = [ 't_regulation' ]
			} else if (alias === 'both_pistol') {
				this.includedPhases = [ 't_pistol', 'ct_pistol' ]
			} else if (alias === 'ct_pistol') {
				this.includedPhases = [ 'ct_pistol' ]
			} else if (alias === 't_pistol') {
				this.includedPhases = [ 't_pistol' ]
			} else if (alias === 'both_overtime') {
				this.includedPhases = [ 't_overtime', 'ct_overtime' ]
			} else if (alias === 'ct_overtime') {
				this.includedPhases = [ 'ct_overtime' ]
			} else if (alias === 't_overtime') {
				this.includedPhases = [ 't_overtime' ]
			}
		},

		sumStats(stats) {
			if (this.includedPhases.length < 2) return stats[this.includedPhases[0]]

			const sum = Object.assign({}, stats[this.includedPhases[0]])

			for (let i = 1; i < this.includedPhases.length; i++) {
				for (const key in stats[this.includedPhases[i]]) {
					sum[key] += stats[this.includedPhases[i]][key]
				}
			}

			return sum
		},

		criterion(criterion, map) {
			if (criterion === 'match_win_pctg') {
				return this.divide(this.n(this.stats[map].matches_won), this.n(this.stats[map].matches_played))
			}

			if (criterion === 'ard') {
				return this.divide(this.n(this.stats[map].round_difference), this.n(this.stats[map].matches_played))
			}

			if (criterion === 'round_win_pctg') {
				return this.divide(this.n(this.stats[map].rounds_won), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'kpr') {
				return this.divide(this.n(this.stats[map].enemy_kills), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'apr') {
				return this.divide(this.n(this.stats[map].enemy_assists), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'dpr') {
				return this.divide(this.n(this.stats[map].deaths), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'kd_ratio') {
				return this.divide(this.n(this.stats[map].enemy_kills), this.n(this.stats[map].deaths))
			}

			if (criterion === 'kill_difference') {
				return this.n(this.stats[map].enemy_kills) - this.n(this.stats[map].deaths)
			}

			if (criterion === 'adr') {
				return this.divide(this.n(this.stats[map].enemy_damage), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'udr') {
				return this.divide(this.n(this.stats[map].enemy_utility_damage), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'hspctg') {
				return this.divide(this.n(this.stats[map].enemy_headshot_kills), this.n(this.stats[map].enemy_kills))
			}

			if (criterion === 'trade_kills_pctg') {
				return this.divide(this.n(this.stats[map].enemy_trade_kills), this.n(this.stats[map].enemy_kills))
			}

			if (criterion === 'deaths_traded_pctg') {
				return this.divide(this.n(this.stats[map].deaths_traded), this.n(this.stats[map].deaths))
			}

			if (criterion === 'kast_pctg') {
				return this.divide(this.n(this.stats[map].kast_rounds), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'time_alive_pctg') {
				return this.divide(this.n(this.stats[map].time_alive_ms), this.n(this.stats[map].max_alive_time_ms))
			}

			if (criterion === '0k_pctg') {
				return this.divide(this.n(this.stats[map]['0_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === '1k_pctg') {
				return this.divide(this.n(this.stats[map]['1_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === '2k_pctg') {
				return this.divide(this.n(this.stats[map]['2_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === '3k_pctg') {
				return this.divide(this.n(this.stats[map]['3_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === '4k_pctg') {
				return this.divide(this.n(this.stats[map]['4_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === '5k_pctg') {
				return this.divide(this.n(this.stats[map]['5_kill_rounds']), this.n(this.stats[map].rounds_played))
			}

			if (criterion === 'clutch_scenarios') {
				return this.n(this.stats[map].one_vs_1_scenarios) + this.n(this.stats[map].one_vs_2_scenarios) + this.n(this.stats[map].one_vs_3_scenarios) + this.n(this.stats[map].one_vs_4_scenarios) + this.n(this.stats[map].one_vs_5_scenarios)
			}

			if (criterion === 'clutch_wins') {
				return this.n(this.stats[map].one_vs_1_wins) + this.n(this.stats[map].one_vs_2_wins) + this.n(this.stats[map].one_vs_3_wins) + this.n(this.stats[map].one_vs_4_wins) + this.n(this.stats[map].one_vs_5_wins)
			}

			if (criterion === 'clutch_win_pctg') {
				const scenarios = (this.n(this.stats[map].one_vs_1_scenarios) + this.n(this.stats[map].one_vs_2_scenarios) + this.n(this.stats[map].one_vs_3_scenarios) + this.n(this.stats[map].one_vs_4_scenarios) + this.n(this.stats[map].one_vs_5_scenarios))

				// always consider no clutch scenarios lower than lost scenarios
				if (scenarios === 0) return -1

				return (this.n(this.stats[map].one_vs_1_wins) + this.n(this.stats[map].one_vs_2_wins) + this.n(this.stats[map].one_vs_3_wins) + this.n(this.stats[map].one_vs_4_wins) + this.n(this.stats[map].one_vs_5_wins)) / scenarios
			}

			if (criterion === 'clutch_kills') {
				return this.n(this.stats[map].one_vs_1_kills) + this.n(this.stats[map].one_vs_2_kills) + this.n(this.stats[map].one_vs_3_kills) + this.n(this.stats[map].one_vs_4_kills) + this.n(this.stats[map].one_vs_5_kills)
			}

			if (criterion === 'enemies_flashed_per_round') {
				return this.n(this.stats[map].enemies_flashed) / this.n(this.stats[map].rounds_played)
			}

			if (criterion === 'avg_enemy_flashed_duration') {
				return this.n(this.stats[map].enemies_flashed_duration) / this.n(this.stats[map].enemies_flashed)
			}

			if (criterion === 'enemy_flashed_duration_per_round') {
				return this.n(this.stats[map].enemies_flashed_duration) / this.n(this.stats[map].rounds_played)
			}

			return this.n(this.stats[map][criterion])
		},

		calculateStatsForPlayer(allStats) {
			const perMap = {}
			const total = {}

			for (const ladder of this.includedLadders) {
				for (const map of this.includedMaps) {
					if (! perMap.hasOwnProperty(map)) perMap[map] = {}

					for (const phase of this.includedPhases) {
						if (! allStats.hasOwnProperty(ladder)
							|| ! allStats[ladder].hasOwnProperty(map)
							|| ! allStats[ladder][map].hasOwnProperty(phase)) continue

						for (const key in allStats[ladder][map][phase]) {
							if (total[key] === undefined) total[key] = 0
							total[key] += allStats[ladder][map][phase][key]

							if (perMap[map][key] === undefined) perMap[map][key] = 0
							perMap[map][key] += allStats[ladder][map][phase][key]
						}
					}
				}
			}

			return [ perMap, total ]
		},
	},

	computed: {
		stats() {
			if (this.rowsAre === 'maps') {
				const [ perMap, total ] = this.calculateStatsForPlayer(this.allStats)
				perMap.total = total

				return perMap
			}

			const stats = {}

			for (const id in this.allPlayers) {
				stats[id] = this.calculateStatsForPlayer(this.allStats[id])[1]
			}

			return stats
		},

		weaponKills() {
			const weapons = {}

			for (const player in (this.rowsAre === 'players' ? this.stats : { total: null })) {
				for (let stat in this.stats[player]) {
					if (! stat.startsWith('enemy_kills_')) continue

					stat = stat.substring(12)
					if (! weapons.hasOwnProperty(stat)) weapons[stat] = 0
					weapons[stat] += this.stats[player][`enemy_kills_${stat}`]
				}
			}

			const kills = []

			for (const weapon in weapons) {
				kills.push([ weapon, weapons[weapon] ])
			}

			return kills.filter(([ _, k ]) => k !== 0).sort(([ _, a ], [ _1, b ]) => {
				if (a < b) return 1
				return (a === b) ? 0 : -1
			})
		},

		includedMapsSorted() {
			const maps = (this.rowsAre === 'maps')
				? this.includedMaps.slice()
				: Object.keys(this.allPlayers)

			if (this.rowsAre === 'maps') maps.push('total')

			const higher = (this.order[1]) ? 1 : -1
			const lower = (this.order[1]) ? -1 : 1

			const fallbackCriterion = (this.rowsAre === 'players')
				? 'adr'
				: 'matches_played'

			return maps.sort((a, b) => {
				let cA = this.criterion(this.order[0], a)
				let cB = this.criterion(this.order[0], b)

				if (cA === cB && this.order[0] !== fallbackCriterion) {
					cA = this.criterion(fallbackCriterion, a)
					cB = this.criterion(fallbackCriterion, b)
				}

				if (cA === cB) return 0

				return (cA > cB)
					? higher
					: lower
			})
		},

		firstRow() {
			if (this.rowsAre === 'maps') return this.stats.total

			for (const key in this.stats) {
				return this.stats[key]
			}
		},
	},

	watch: {
		includedLadders(ladders) {
			set('included_ladders', ladders)
		},

		includedMaps(maps) {
			set('included_maps', maps)
		},
	},
}
</script>
