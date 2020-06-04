<template>
	<tr>
		<td :title="map">
			<div class="text-ellipsis">
				{{ map }}
			</div>
		</td>

		<td>
			{{ n(stats.matches_played) / includedPhases }}
		</td>

		<td>
			{{ n(stats.matches_won) / includedPhases }}
		</td>

		<td>
			{{ nf(divide(stats.matches_won, stats.matches_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ n(stats.matches_tied) / includedPhases }}
		</td>

		<td>
			{{ n(stats.matches_lost) / includedPhases }}
		</td>

		<td>
			{{ nf(divide(stats.round_difference, stats.matches_played), 2) }}
		</td>

		<td>
			{{ n(stats.rounds_played) }}
		</td>

		<td>
			{{ n(stats.rounds_won) }}
		</td>

		<td>
			{{ nf(divide(stats.rounds_won, stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ n(stats.enemy_kills) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_kills, stats.rounds_played), 2) }}
		</td>

		<td>
			{{ n(stats.enemy_assists) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_assists, stats.rounds_played), 2) }}
		</td>

		<td>
			{{ n(stats.deaths) }}
		</td>

		<td>
			{{ nf(divide(stats.deaths, stats.rounds_played), 2) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_kills, stats.deaths), 2) }}
		</td>

		<td>
			{{ n(stats.enemy_kills) - n(stats.deaths) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_damage, stats.rounds_played), 1) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_utility_damage, stats.rounds_played), 1) }}
		</td>

		<td>
			{{ nf(divide(stats.enemy_headshot_kills, stats.enemy_kills) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats.kast_rounds, stats.rounds_played) * 100, 1) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats.time_alive_ms, stats.max_alive_time_ms) * 100, 1) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['0_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['1_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['2_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['3_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['4_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ nf(divide(stats['5_kill_rounds'], stats.rounds_played) * 100, 0) }}&nbsp;%
		</td>

		<td>
			{{ n(stats['5_kill_rounds']) }}
		</td>

		<td>
			{{
				n(stats.one_vs_1_scenarios)
					+ n(stats.one_vs_2_scenarios)
					+ n(stats.one_vs_3_scenarios)
					+ n(stats.one_vs_4_scenarios)
					+ n(stats.one_vs_5_scenarios)
			}}
		</td>

		<td>
			{{
				n(stats.one_vs_1_wins)
					+ n(stats.one_vs_2_wins)
					+ n(stats.one_vs_3_wins)
					+ n(stats.one_vs_4_wins)
					+ n(stats.one_vs_5_wins)
			}}
		</td>

		<td>
			{{
				nf(divide(
					n(stats.one_vs_1_wins)
						+ n(stats.one_vs_2_wins)
						+ n(stats.one_vs_3_wins)
						+ n(stats.one_vs_4_wins)
						+ n(stats.one_vs_5_wins),
					n(stats.one_vs_1_scenarios)
						+ n(stats.one_vs_2_scenarios)
						+ n(stats.one_vs_3_scenarios)
						+ n(stats.one_vs_4_scenarios)
						+ n(stats.one_vs_5_scenarios)
				) * 100, 1)
			}}&nbsp;%
		</td>

		<td>
			{{
				n(stats.one_vs_1_kills)
					+ n(stats.one_vs_2_kills)
					+ n(stats.one_vs_3_kills)
					+ n(stats.one_vs_4_kills)
					+ n(stats.one_vs_5_kills)
			}}
		</td>
	</tr>
</template>

<script>
import MathHelpers from '../Mixins/MathHelpers'

export default {
	mixins: [ MathHelpers ],

	props: [ 'includedPhases', 'map', 'stats' ],
}
</script>
