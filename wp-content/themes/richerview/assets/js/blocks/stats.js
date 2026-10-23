/**
 * Stats – count each number from its "Count from" value to its real value
 * when the block scrolls into view (up or down).
 *
 * The real number is already in the page (for search engines, screen readers
 * and visitors without JavaScript); this only animates the visible copy.
 */
(function () {
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const DURATION = 2000;

	// Fast start, gentle finish
	function ease(p) {
		return 1 - Math.pow(1 - p, 3);
	}

	function format(value, el) {
		const decimals = parseInt(el.dataset.decimals, 10) || 0;
		const number = value.toLocaleString('en-GB', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
			useGrouping: el.dataset.separator === '1',
		});
		return el.dataset.prefix + number + el.dataset.suffix;
	}

	function count(el) {
		const from = parseFloat(el.dataset.countFrom) || 0;
		const to = parseFloat(el.dataset.countTo) || 0;
		const final_text = el.textContent;
		let start = null;

		function step(time) {
			if (start === null) start = time;
			const p = Math.min(1, (time - start) / DURATION);
			el.textContent = p < 1 ? format(from + (to - from) * ease(p), el) : final_text;
			if (p < 1) requestAnimationFrame(step);
		}
		requestAnimationFrame(step);
	}

	function init(section) {
		const counters = Array.from(section.querySelectorAll('.b-stats__count'));
		if (!counters.length || reduced_motion.matches || !('IntersectionObserver' in window)) return;

		// Show the starting values until the block comes into view
		const finals = counters.map(function (el) {
			return el.textContent;
		});
		counters.forEach(function (el) {
			el.textContent = format(parseFloat(el.dataset.countFrom) || 0, el);
		});

		const observer = new IntersectionObserver(function (entries) {
			if (!entries[0].isIntersecting) return;
			observer.disconnect();
			counters.forEach(function (el, i) {
				el.textContent = finals[i];
				count(el);
			});
		}, { threshold: 0.4 });
		observer.observe(section);
	}

	function setup() {
		document.querySelectorAll('.b-stats').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
