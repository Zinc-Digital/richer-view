/**
 * Service Hero – the photo/video grows from its starting size (1190 × 555 in
 * the design) to the full width of the screen (1440 × 864) as the page is
 * scrolled, and plays the optional background video
 * (assets/js/components/background-video.js).
 *
 * The media box takes the full-size space straight away so nothing below it
 * moves; the frame inside is scaled down and cropped top and bottom, then
 * eased back to full size. Only transform and clip-path change while
 * scrolling, which keeps it smooth.
 *
 * Growing starts when the box is where it sits on load (or 3/4 of the way down
 * the screen, if the block is further down a page) and finishes as its top
 * reaches the top of the screen. Visitors who prefer reduced motion get the
 * starting size, without the effect.
 */
(function () {
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');

	// Starting width in the design, and the space either side of it on small screens
	const START_WIDTH = 1190;
	const GUTTER = 25;

	function init(media) {
		const frame = media.querySelector('.b-service-hero__frame');
		if (!frame) return;

		media.classList.add('is-expanding');

		let start_top = 0;      // where the box's top is (from the top of the screen) when growing starts
		let distance = 1;       // how far to scroll to reach full size
		let start_ratio = 0;
		let end_ratio = 0;
		let frame_pending = false;

		function measure() {
			const styles = getComputedStyle(media);
			start_ratio = parseFloat(styles.getPropertyValue('--start-ratio')) || 0.46639;
			// The full-size shape can't be shorter than the starting shape (the crop would go negative)
			end_ratio = Math.max(start_ratio, parseFloat(styles.getPropertyValue('--end-ratio')) || 0.6);

			const page_top = media.getBoundingClientRect().top + window.scrollY;
			start_top = Math.min(page_top, window.innerHeight * 0.75);
			distance = Math.max(start_top, 300);
		}

		function render() {
			frame_pending = false;

			const full_width = media.offsetWidth;
			const full_height = full_width * end_ratio;
			const start_width = Math.min(START_WIDTH, full_width - GUTTER * 2);
			const start_height = start_width * start_ratio;

			const progress = Math.min(1, Math.max(0, (start_top - media.getBoundingClientRect().top) / distance));

			// Visible size now, then the scale and the crop (in the frame's own pixels) that give it
			const width = start_width + (full_width - start_width) * progress;
			const height = start_height + (full_height - start_height) * progress;
			const scale = width / full_width;
			const crop = Math.max(0, (full_height - height / scale) / 2);

			frame.style.transform = 'translateY(' + (-crop * scale).toFixed(2) + 'px) scale(' + scale.toFixed(4) + ')';
			frame.style.clipPath = 'inset(' + crop.toFixed(2) + 'px 0)';
		}

		function request_render() {
			if (frame_pending) return;
			frame_pending = true;
			requestAnimationFrame(render);
		}

		measure();
		render();
		window.addEventListener('scroll', request_render, { passive: true });
		window.addEventListener('resize', function () {
			measure();
			request_render();
		});
		// Fonts and images loading above can move the box
		window.addEventListener('load', function () {
			measure();
			request_render();
		});
	}

	function setup() {
		document.querySelectorAll('.b-service-hero__media').forEach(function (media) {
			if (media.hasAttribute('data-expand') && !reduced_motion.matches) {
				init(media);
			}

			const video = media.querySelector('.b-service-hero__video');
			if (video && window.zincBackgroundVideo) {
				window.zincBackgroundVideo(video, { observe: media });
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
