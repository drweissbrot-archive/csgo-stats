<template>
	<td colspan="35" class="round-graph">
		<div class="team-a">
			<div v-for="(round, i) in rounds" :class="'col --' + ((round.winner === 'a') ? round.side : `${otherSide(round.side)}-color`)">
				<div class="cell">
					<survived-svg :survived="round.survived_a" />
				</div>

				<div class="cell">
					<template v-if="round.winner === 'a'">
						<round-win-reason :icons="icons" :reason="round.reason" />
					</template>
				</div>
			</div>
		</div>

		<div class="round">
			<div v-for="(round, i) in rounds" class="col">
				<template v-if="[0, 4, 9, 14, 19, 24, 29].includes(i)">
					{{ i + 1 }}
				</template>

				<template v-else-if="i > 29 && (i - 18) % 6 === 0">
					+{{ (i - 24) / 6 }}
				</template>

				<template v-else-if="i > 29 && (i - 18) % 3 === 0">
					{{ i + 1 }}
				</template>
			</div>
		</div>

		<div class="team-b">
			<div v-for="(round, i) in rounds" :class="'col --' + ((round.winner === 'b') ? round.side : `${otherSide(round.side)}-color`)">
				<div class="cell">
					<template v-if="round.winner === 'b'">
						<round-win-reason :icons="icons" :reason="round.reason" />
					</template>

					<template v-else>
						&nbsp;
					</template>
				</div>

				<div class="cell">
					<survived-svg :survived="round.survived_b" :topToBottom="true" />
				</div>
			</div>
		</div>
	</td>
</template>

<script>
export default {
	props: [ 'icons', 'rounds' ],

	methods: {
		otherSide(team) {
			if (team === 'ct') return 't'
			if (team === 't') return 'ct'

			throw false
		},
	},
}
</script>
