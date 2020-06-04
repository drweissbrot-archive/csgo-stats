<template>
	<div class="weapon-kills-chart" :style="style">
		<div v-for="([ weapon, kills ], i) in weaponKills" class="weapon-wrapper">
			<div :class="[ 'weapon', { '--empty': columns[i] < .02 } ]" :title="weapon">
				<img v-if="columns[i] >= .02" :src="`/images/${weapon}.svg`" :alt="weapon">

				<template v-if="columns[i] >= .055">
					{{ kills }}
					({{ nf(kills / totalKills * 100, 0, 2) }}&nbsp;%)
				</template>
			</div>

			<div class="tooltip-wrapper" v-if="columns[i] < .055">
				<div class="tooltip">
					<img :src="`/images/${weapon}.svg`" :alt="weapon">
					{{ kills }}
					({{ nf(kills / totalKills * 100, 0, 2) }}&nbsp;%)
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import MathHelpers from '../Mixins/MathHelpers'

export default {
	mixins: [ MathHelpers ],

	props: [ 'weaponKills', 'totalKills' ],

	computed: {
		columns() {
			const columns = []

			for (const [ weapon, kills ] of this.weaponKills) {
				columns.push(kills / this.totalKills)
			}

			return columns
		},

		style() {
			return {
				gridTemplateColumns: this.columns.map((col) => {
					return (col * 100) + '%'
				}).join(' '),
			}
		},
	},
}
</script>
