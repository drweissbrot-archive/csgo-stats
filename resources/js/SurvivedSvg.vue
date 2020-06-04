<template>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 31.5" version="1.1">
		<g>
			<rect width="22.5" height="2.5" x="1" y="0" :class="{ 'dead': survived < numbers[0] }" />

			<rect width="22.5" height="2.5" x="1" y="7" :class="{ 'dead': survived < numbers[1] }" />

			<rect width="22.5" height="2.5" x="1" y="14" :class="{ 'dead': survived < numbers[2] }" />

			<rect width="22.5" height="2.5" x="1" y="21" :class="{ 'dead': survived < numbers[3] }" />

			<rect width="22.5" height="2.5" x="1" y="28" :class="{ 'dead': survived < numbers[4] }" />
		</g>
	</svg>
</template>

<script>
export default {
	props: [ 'survived', 'topToBottom' ],

	computed: {
		numbers() {
			return (this.topToBottom)
				? [1, 2, 3, 4, 5]
				: [5, 4, 3, 2, 1]
		},
	},
}
</script>
