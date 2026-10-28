/**
 * Drag slider – a horizontally scrolling row that can be dragged with the mouse.
 *
 * - Starts at the beginning, centre or end of the row, or a position worked out
 *   by a function (until the visitor scrolls it).
 * - Mouse drag with a little momentum when let go; touch screens swipe natively
 *   and keyboards use the arrow keys (the row should have tabindex="0").
 * - A drag doesn't also count as a click on a link/button inside the row.
 * - If the section has a cursor badge (.c-cursor, see cursor-badge.js), it shows
 *   its "‹ drag ›" content over the row.
 *
 * Adds classes to the section: is-draggable, is-dragging, has-cursor.
 *
 * Usage:
 *   const slider = window.zincDragSlider(section, row, { start: 'centre' });
 *   slider.is_dragging();               // true mid-drag
 *   slider.show_label(event, 'Play');   // badge shows other text (e.g. over a video)
 *   slider.show_drag(event);            // back to "‹ drag ›"
 *   slider.hide_badge();                // normal mouse pointer (e.g. over a link)
 */
(function () {
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');

	// How far (px) the mouse has to move before a press counts as a drag, not a click
	const DRAG_THRESHOLD = 5;

	window.zincDragSlider = function (section, slider, options) {
		const start = (options && options.start) || 'start';
		const noop = function () {};
		const api = { is_dragging: function () { return false; }, show_drag: noop, show_label: noop, hide_badge: noop };
		if (!section || !slider) return api;

		let dragging = false;
		let moved = false;
		let touched = false;
		let start_x = 0;
		let start_scroll = 0;
		let last_x = 0;
		let last_time = 0;
		let velocity = 0;
		let momentum = null;

		// Starting position, kept until the visitor scrolls the row themselves.
		// `start` can also be a function returning the scroll position in px.
		function start_position() {
			if (typeof start === 'function') return start(slider);
			const max = slider.scrollWidth - slider.clientWidth;
			return start === 'end' ? max : start === 'centre' ? max / 2 : 0;
		}
		// The position is worked out once per load/resize (working it out can change the
		// layout, so it isn't repeated on every scroll)
		let target = 0;
		let positioning = false;
		function position() {
			if (touched) return;
			positioning = true;
			target = start_position();
			slider.scrollLeft = target;
			requestAnimationFrame(function () {
				positioning = false;
			});
		}
		position();
		window.addEventListener('load', position);
		window.addEventListener('resize', position);
		slider.addEventListener('scroll', function () {
			if (!dragging && !positioning && Math.abs(slider.scrollLeft - target) > 2) touched = true;
		}, { passive: true });

		// Mouse dragging
		section.classList.add('is-draggable');

		slider.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'mouse' || e.button !== 0) return;
			cancelAnimationFrame(momentum);
			dragging = true;
			moved = false;
			touched = true;
			start_x = last_x = e.clientX;
			start_scroll = slider.scrollLeft;
			last_time = performance.now();
			velocity = 0;
		});

		window.addEventListener('pointermove', function (e) {
			if (!dragging) return;
			const distance = e.clientX - start_x;
			if (!moved && Math.abs(distance) > DRAG_THRESHOLD) {
				moved = true;
				section.classList.add('is-dragging');
			}
			if (!moved) return;

			slider.scrollLeft = start_scroll - distance;

			const now = performance.now();
			velocity = (e.clientX - last_x) / Math.max(1, now - last_time);
			last_x = e.clientX;
			last_time = now;
		});

		window.addEventListener('pointerup', function () {
			if (!dragging) return;
			dragging = false;
			section.classList.remove('is-dragging');
			if (!moved || reduced_motion.matches) return;

			// Keep gliding briefly in the direction of the drag
			let speed = velocity * 16;
			(function glide() {
				speed *= 0.92;
				slider.scrollLeft -= speed;
				if (Math.abs(speed) > 0.5) momentum = requestAnimationFrame(glide);
			})();
		});

		// A drag shouldn't also count as a click
		slider.addEventListener('click', function (e) {
			if (moved) {
				e.preventDefault();
				e.stopPropagation();
				moved = false;
			}
		}, true);

		// Stop links and images being dragged by the browser itself
		slider.addEventListener('dragstart', function (e) {
			e.preventDefault();
		});

		api.is_dragging = function () {
			return dragging;
		};

		// "‹ drag ›" badge in place of the mouse pointer
		const cursor = section.querySelector('.c-cursor');
		const badge = window.zincCursorBadge && window.zincCursorBadge(cursor);
		if (badge) {
			const badge_el = cursor.querySelector('.c-cursor__badge');
			const drag_content = Array.from(badge_el.childNodes).map(function (node) {
				return node.cloneNode(true);
			});

			api.show_drag = function (e) {
				const content = document.createDocumentFragment();
				drag_content.forEach(function (node) {
					content.appendChild(node.cloneNode(true));
				});
				badge_el.classList.add('c-cursor__badge--drag');
				badge.show(e, content);
			};

			api.show_label = function (e, text) {
				const label = document.createElement('span');
				label.className = 'c-cursor__label';
				label.textContent = text;
				badge_el.classList.remove('c-cursor__badge--drag');
				badge.show(e, label);
			};

			api.hide_badge = badge.hide;

			section.classList.add('has-cursor');
			slider.addEventListener('mouseenter', api.show_drag);
			slider.addEventListener('mouseleave', badge.hide);
		}

		return api;
	};
})();
