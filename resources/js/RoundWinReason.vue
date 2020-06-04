<template>
	<img class="win-reason" :src="url" :alt="title" :title="title">
</template>

<script>
// cf. https://github.com/saul/demofile/issues/38#issuecomment-330068048
// cf. https://github.com/alliedmodders/sourcemod/blob/master/plugins/include/cstrike.inc#L51-L75
export default {
	props: [ 'icons', 'reason' ],

	computed: {
		url() {
			if (this.reason === 1) return this.icons.win_bomb
			if (this.reason === 7) return this.icons.win_defuse
			if (this.reason === 8 || this.reason === 9) return this.icons.win_elimination
			if (this.reason === 12) return this.icons.win_timer
			if (this.reason === 17 || this.reason === 18) return this.icons.win_surrender
		},

		title() {
			if (this.reason === 1) return 'Bomb Exploded'
			// if (this.reason === 4) return 'TerroristsEscaped'
			// if (this.reason === 5) return 'CTStoppedEscape'
			// if (this.reason === 6) return 'TerroristsStopped (Escaping Ts eliminated)'
			if (this.reason === 7) return 'Bomb Defused'
			if (this.reason === 8 || this.reason === 9) return 'Eliminated Enemy Team'
			// if (this.reason === 10) return 'Round Draw'
			// if (this.reason === 11) return 'Hostage Rescued'
			if (this.reason === 12) return 'Round Timer Ran Out'
			// if (this.reason === 13) return 'HostagesNotRescued'
			// if (this.reason === 14) return 'TerroristsNotEscaped'
			// if (this.reason === 16) return 'GameCommencing'
			if (this.reason === 17 || this.reason === 18) return 'Enemy Team Surrendered'
			// if (this.reason === 19) return 'TerroristsPlanted'
			// if (this.reason === 20) return 'CTsReachedHostage'
		},
	},
}
</script>
