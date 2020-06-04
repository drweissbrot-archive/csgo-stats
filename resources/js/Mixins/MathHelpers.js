export default {
	methods: {
		nf(number, minDigits = 2, maxDigits = null, config = {}) {
			return new Intl.NumberFormat('en-US', Object.assign({
				minimumFractionDigits: minDigits,
				maximumFractionDigits: (maxDigits === null) ? minDigits : maxDigits,
			}, config)).format(number)
		},

		divide(dividend, divisor) {
			if (dividend === 0 || dividend === null || dividend === undefined) return 0
			if (divisor === 0 || divisor === null || divisor === undefined) return Infinity

			return dividend / divisor
		},

		n(number) {
			return (number === null || number === undefined)
				? 0
				: number
		},
	}
}
