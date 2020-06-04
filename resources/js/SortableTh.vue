<template>
	<th :title="title">
		<a href="#" :class="className" @click.prevent="sort">
			<slot></slot>
		</a>
	</th>
</template>

<script>
export default {
	props: [ 'criterion', 'title', 'order' ],

	methods: {
		sort() {
			if (this.order[0] === this.criterion) {
				return this.$emit('input', [ this.criterion, ! this.order[1] ])
			}

			this.$emit('input', [ this.criterion, false ])
		},
	},

	computed: {
		className() {
			if (this.order[0] !== this.criterion) return

			return (this.order[1])
				? 'is-asc'
				: 'is-desc'
		}
	},
}
</script>
