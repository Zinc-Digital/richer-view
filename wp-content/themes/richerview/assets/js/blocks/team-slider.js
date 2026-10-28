/**
 * Team Slider – draggable row of team members with the "‹ drag ›" cursor badge
 * (assets/js/components/drag-slider.js).
 *
 * On desktop the row starts as in the design: the first card runs off the left
 * edge of the screen by the same share of the screen width (106px at 1440px),
 * and dragging right reveals it. Smaller screens (one card in view), or the
 * "Start" setting, begin with the first card lined up with the heading.
 */
(function () {
	const desktop = window.matchMedia('(min-width: 1025px)');

	// How much of the first card is off the left edge in the design: 106px of a 1440px screen
	const BLEED = 106 / 1440;

	function setup() {
		if (!window.zincDragSlider) return;

		document.querySelectorAll('.b-team').forEach(function (section) {
			const row = section.querySelector('.b-team__slider');
			const track = section.querySelector('.b-team__track');
			const first = section.querySelector('.b-team__item');
			if (!row || !track || !first) return;

			function bleed_position() {
				// Reset any extra room added last time, then work out where the first card naturally sits
				track.style.paddingRight = '';
				const natural = first.getBoundingClientRect().left - row.getBoundingClientRect().left + row.scrollLeft;
				const target = natural + window.innerWidth * BLEED;

				// On wide screens the row may be too short to scroll that far – add room at the end
				const extra = target + row.clientWidth - track.offsetWidth;
				if (extra > 0) {
					track.style.paddingRight = (parseFloat(getComputedStyle(track).paddingRight) + extra) + 'px';
				}
				return target;
			}

			const slider = window.zincDragSlider(section, row, {
				start: function () {
					if (section.dataset.start === 'start' || !desktop.matches) {
						track.style.paddingRight = '';
						return 0;
					}
					return bleed_position();
				},
			});

			// Over the careers link: the normal hand pointer instead of the "drag" badge
			const link = section.querySelector('.b-team__join-link');
			if (link) {
				link.addEventListener('mouseenter', function () {
					if (!slider.is_dragging()) slider.hide_badge();
				});
				link.addEventListener('mouseleave', function (e) {
					if (row.contains(e.relatedTarget)) slider.show_drag(e);
				});
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
