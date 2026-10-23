/**
 * Cursor badge – a round badge that replaces the mouse pointer and follows it
 * (styles: assets/sass/components/_c.cursor-badge.scss).
 *
 * Usage:
 *   const badge = window.zincCursorBadge(element); // .c-cursor element, or null on touch screens
 *   badge.show(event, content);                      // content: optional Node to put in the badge
 *   badge.hide();
 */
(function () {
	const can_hover = window.matchMedia('(hover: hover) and (pointer: fine)');
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');

	window.zincCursorBadge = function (cursor) {
		if (!cursor || !can_hover.matches) return null;

		const badge = cursor.querySelector('.c-cursor__badge');
		const follow = reduced_motion.matches ? 1 : 0.25;
		let x = 0, y = 0, current_x = 0, current_y = 0, frame = null;

		function move_to(new_x, new_y) {
			cursor.style.transform = 'translate3d(' + new_x + 'px, ' + new_y + 'px, 0)';
		}

		// Ease towards the mouse for a smooth trailing motion
		function render() {
			frame = null;
			current_x += (x - current_x) * follow;
			current_y += (y - current_y) * follow;
			move_to(current_x, current_y);
			if (Math.abs(x - current_x) > 0.1 || Math.abs(y - current_y) > 0.1) {
				frame = requestAnimationFrame(render);
			}
		}

		window.addEventListener('mousemove', function (e) {
			x = e.clientX;
			y = e.clientY;
			if (!frame) frame = requestAnimationFrame(render);
		}, { passive: true });

		return {
			show: function (e, content) {
				if (content) badge.replaceChildren(content);
				if (!cursor.classList.contains('is-visible')) {
					x = current_x = e.clientX;
					y = current_y = e.clientY;
					move_to(x, y);
				}
				cursor.classList.add('is-visible');
			},
			hide: function () {
				cursor.classList.remove('is-visible');
			},
		};
	};
})();
