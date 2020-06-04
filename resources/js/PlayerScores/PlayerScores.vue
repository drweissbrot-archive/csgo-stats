<template>
	<div v-if="loaded" class="player-scores">
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
						:checked="includedLadders.includes(ladder)"
						@change.prevent="toggleIncludedLadder(ladder)"
					>
					{{ ladder }}
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
					<headings v-model="order" />
				</thead>

				<tbody>
					<scores-row v-if="map === 'total' || allMaps.hasOwnProperty(map)"
						:map="(map === 'total') ? 'All Maps' : allMaps[map].display_name"
						v-for="map in includedMapsSorted" :key="map"
						:stats="stats[map]"
						:includedPhases="includedPhases.length"
					/>
				</tbody>
			</table>
		</section>

		<section>
			<h2>Weapons</h2>

			<weapon-kills-chart :totalKills="stats.total.enemy_kills" :weaponKills="weaponKills" />
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

	props: [ 'allLadders', 'allMaps', 'allStats' ],

	components: {
		Headings,
		ScoresRow,
		WeaponKillsChart,
	},

	async mounted() {
		this.includedLadders = await get('included_ladders') || this.allLadders.slice()
		this.includedMaps = await get('included_maps') || Object.keys(this.allMaps)
		this.loaded = true
	},

	data() {
		return {
			loaded: false,
			includedLadders: [],
			includedMaps: [],
			includedPhases: [ 't_regulation', 'ct_regulation', 't_overtime', 'ct_overtime' ],

			order: [ 'match_win_pctg', false ],
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

			return this.n(this.stats[map][criterion])
		},
	},

	computed: {
		stats() {
			const stats = { total: {} }

			for (const ladder of this.includedLadders) {
				for (const map of this.includedMaps) {
					if (! stats.hasOwnProperty(map)) stats[map] = {}

					for (const phase of this.includedPhases) {
						if (! this.allStats.hasOwnProperty(ladder)
							|| ! this.allStats[ladder].hasOwnProperty(map)
							|| ! this.allStats[ladder][map].hasOwnProperty(phase)) continue

						for (const key in this.allStats[ladder][map][phase]) {
							if (stats.total[key] === undefined) stats.total[key] = 0
							stats.total[key] += this.allStats[ladder][map][phase][key]

							if (stats[map][key] === undefined) stats[map][key] = 0
							stats[map][key] += this.allStats[ladder][map][phase][key]
						}
					}
				}
			}

			return stats
		},

		weaponKills() {
			const weapons = []

			for (const stat in this.stats.total) {
				if (! stat.startsWith('enemy_kills_')) continue

				weapons.push([ stat.substring(12), this.stats.total[stat] ])
			}

			return weapons.filter(([ _, k ]) => k !== 0).sort(([ _, a ], [ _1, b ]) => {
				if (a < b) return 1
				return (a === b) ? 0 : -1
			})
		},

		includedMapsSorted() {
			const maps = this.includedMaps.slice()
			maps.push('total')

			const higher = (this.order[1]) ? 1 : -1
			const lower = (this.order[1]) ? -1 : 1

			return maps.sort((a, b) => {
				let cA = this.criterion(this.order[0], a)
				let cB = this.criterion(this.order[0], b)

				if (cA === cB && this.order[0] !== 'matches_played') {
					cA = this.criterion('matches_played', a)
					cB = this.criterion('matches_played', b)
				}

				if (cA === cB) return 0

				return (cA > cB)
					? higher
					: lower
			})
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
