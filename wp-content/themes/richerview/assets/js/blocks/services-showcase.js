/**
 * Services Showcase – scroll animation (desktop only), card videos and hover badge.
 *
 * 1. The heading sticks near the top of the screen, then shrinks and fades
 *    out as the cards scroll up over it.
 * 2. The card row sticks in the middle of the screen and slides sideways as
 *    the page scrolls, until the last card is in view.
 *
 * Below 1025px, or when the visitor prefers reduced motion, nothing is
 * changed and the cards stay a swipeable row.
 *
 * With the scroll animation turned off, the row can also be dragged left and
 * right with the mouse (assets/js/components/drag-slider.js).
 */
(function () {
	const desktop = window.matchMedia('(min-width: 1025px)');
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');

	// Scroll distance (px) over which the heading fades out
	const FADE_DISTANCE = 500;

	// Slow start and finish, steady in the middle
	function ease(p) {
		if (p < 0.3) {
			const s = p / 0.3;
			return 0.214 * s * s;
		}
		if (p > 0.7) {
			const s = (1 - p) / 0.3;
			return 1 - 0.214 * s * s;
		}
		return 0.214 + ((p - 0.3) / 0.4) * (1 - 2 * 0.214);
	}

	function clamp(value) {
		return Math.max(0, Math.min(1, value));
	}

	function init(section) {
		const intro = section.querySelector('.b-services__intro');
		const slider = section.querySelector('.b-services__slider');
		const viewport = section.querySelector('.b-services__viewport');
		const track = section.querySelector('.b-services__track');
		if (!slider || !viewport || !track) return;

		let active = false;
		let distance = 0;
		let viewport_top = 0;
		let frame = null;

		function reset() {
			section.classList.remove('is-animated');
			section.style.removeProperty('--b-services-intro-top');
			slider.style.height = '';
			track.style.transform = '';
			if (intro) {
				intro.style.transform = '';
				intro.style.opacity = '';
			}
		}

		function measure() {
			reset();
			active = desktop.matches && !reduced_motion.matches;
			if (!active) return;

			// The heading sticks 15% down the screen, or where it already sits if the block is at the top of the page
			if (intro) {
				const intro_top = intro.getBoundingClientRect().top + window.scrollY;
				section.style.setProperty('--b-services-intro-top', Math.min(window.innerHeight * 0.15, intro_top) + 'px');
			}

			section.classList.add('is-animated');
			viewport.scrollLeft = 0;

			// How far the row has to travel for the last card to line up with the right gutter
			const bounds = viewport.getBoundingClientRect();
			const right_edge = bounds.right - parseFloat(getComputedStyle(viewport).paddingRight);
			distance = Math.max(0, track.lastElementChild.getBoundingClientRect().right - right_edge);

			// Pin the cards in the middle of the screen while they slide
			const height = viewport.offsetHeight;
			viewport_top = Math.max(0, (window.innerHeight - height) / 2);
			section.style.setProperty('--b-services-viewport-top', viewport_top + 'px');
			slider.style.height = (height + distance) + 'px';

			update();
		}

		function update() {
			frame = null;
			if (!active) return;

			if (intro) {
				const intro_top = parseFloat(getComputedStyle(intro).top) || 0;
				const natural_top = section.getBoundingClientRect().top + parseFloat(getComputedStyle(section).paddingTop);
				const p = clamp((intro_top - natural_top) / FADE_DISTANCE);
				const opacity = Math.max(0, 1 - p * 1.3);

				intro.style.transform = p > 0 ? 'scale(' + (1 - p * 0.12) + ') translateZ(0)' : '';
				intro.style.opacity = p > 0 ? String(opacity) : '';
			}

			if (distance > 0) {
				const p = clamp((viewport_top - slider.getBoundingClientRect().top) / distance);
				track.style.transform = 'translateX(' + (-ease(p) * distance) + 'px) translateZ(0)';
			}
		}

		function request_update() {
			if (!frame) frame = requestAnimationFrame(update);
		}

		let resize_timer = null;
		function request_measure() {
			clearTimeout(resize_timer);
			resize_timer = setTimeout(measure, 150);
		}

		window.addEventListener('scroll', request_update, { passive: true });
		window.addEventListener('resize', request_measure);
		desktop.addEventListener('change', measure);
		reduced_motion.addEventListener('change', measure);
		window.addEventListener('load', measure);
		if (document.fonts) document.fonts.ready.then(measure);

		measure();
	}

	/**
	 * Card background videos (YouTube, Vimeo or uploaded file) – shared
	 * assets/js/components/background-video.js. Cards set to "Play on hover"
	 * only show their video while the mouse is over the card.
	 */
	function init_videos(section) {
		if (!window.zincBackgroundVideo) return;
		section.querySelectorAll('.b-services__video').forEach(function (wrapper) {
			const card = wrapper.closest('.b-services__card');
			window.zincBackgroundVideo(wrapper, {
				observe: card,
				hover_target: wrapper.dataset.videoPlayback === 'hover' ? card : null,
			});
		});
	}

	/**
	 * Hover badge – shows each card's text/icon (from its
	 * <template class="b-services__cursor-template">) in the shared cursor badge
	 * (assets/js/components/cursor-badge.js). Mouse only.
	 */
	function init_cursor(section) {
		const badge = window.zincCursorBadge && window.zincCursorBadge(section.querySelector('.c-cursor'));
		if (!badge) return;

		section.classList.add('has-cursor');

		section.querySelectorAll('.b-services__item').forEach(function (item) {
			const template = item.querySelector('.b-services__cursor-template');
			const card = item.querySelector('.b-services__card');
			if (!template || !card) return;

			card.addEventListener('mouseenter', function (e) {
				badge.show(e, template.content.cloneNode(true));
			});
			card.addEventListener('mouseleave', badge.hide);
		});
	}

	function setup() {
		document.querySelectorAll('.b-services[data-scroll-animation]').forEach(init);
		document.querySelectorAll('.b-services:not([data-scroll-animation])').forEach(function (section) {
			// The cards keep their own hover badges, so no "drag" badge
			if (window.zincDragSlider) window.zincDragSlider(section, section.querySelector('.b-services__viewport'), { badge: false });
		});
		document.querySelectorAll('.b-services').forEach(function (section) {
			init_videos(section);
			init_cursor(section);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
