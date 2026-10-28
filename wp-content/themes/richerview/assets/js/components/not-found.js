/**
 * 404 page (404.php) – the round "that's not the shot." badge drifts after the
 * mouse while it's over the page and glides back to its place when it leaves
 * (mouse only; it stays put for visitors who prefer reduced motion), and the
 * optional background video plays (background-video.js).
 */
(function () {
	const can_hover = window.matchMedia('(hover: hover) and (pointer: fine)');
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const FOLLOW = 0.12; // how quickly the badge catches up with the mouse each frame

	function init(section) {
		const video = section.querySelector('.c-not-found__video');
		if (video && window.zincBackgroundVideo) {
			window.zincBackgroundVideo(video, { observe: section });
		}

		const badge = section.querySelector('.c-not-found__badge');
		if (!badge || !can_hover.matches || reduced_motion.matches) return;

		// Offset from the badge's own place (0, 0 = where the design puts it)
		let target_x = 0, target_y = 0, x = 0, y = 0, frame = null;

		function render() {
			x += (target_x - x) * FOLLOW;
			y += (target_y - y) * FOLLOW;
			badge.style.transform = 'translate3d(' + x.toFixed(1) + 'px, ' + y.toFixed(1) + 'px, 0)';
			frame = Math.abs(target_x - x) > 0.2 || Math.abs(target_y - y) > 0.2 ? requestAnimationFrame(render) : null;
		}

		function go(new_x, new_y) {
			target_x = new_x;
			target_y = new_y;
			if (!frame) frame = requestAnimationFrame(render);
		}

		section.classList.add('has-badge-follow');

		section.addEventListener('mousemove', function (e) {
			// Over the link the normal pointer shows and the badge heads home
			if (e.target.closest('a')) {
				go(0, 0);
				return;
			}
			const home = badge.getBoundingClientRect();
			go(e.clientX - (home.left - x + home.width / 2), e.clientY - (home.top - y + home.height / 2));
		});
		section.addEventListener('mouseleave', function () {
			go(0, 0);
		});
	}

	function setup() {
		document.querySelectorAll('.c-not-found').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
