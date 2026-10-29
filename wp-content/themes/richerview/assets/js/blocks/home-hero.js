/**
 * Home Hero – zooms out from the main photo filling the screen to the grid of
 * photos and videos as the page is scrolled, plays the tiles' background
 * videos (assets/js/components/background-video.js), and opens a tile's video
 * or photo in a pop-up when it's clicked, with a "zoom" badge over the tiles.
 *
 * The block grows by one screen height of scrolling; meanwhile the stage sticks
 * to the top of the screen and the grid is scaled from the main tile covering
 * the screen (centred a little below the middle, as in the design) down to its
 * real size. The wordmark and intro text fade out over the first third. Once
 * zoomed out the page scrolls on as normal, showing the last row of tiles.
 *
 * The site header (which sits at the top of the page) is carried down with
 * the stage while it sticks, so the logo and menu show over the zoomed-out grid
 * as in the design; afterwards it scrolls away as usual.
 *
 * Only transform and opacity change while scrolling, which keeps it smooth.
 * Without grid tiles, with the zoom turned off, or for visitors who prefer
 * reduced motion, it stays the full-screen photo.
 */
(function () {
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');

	// The main tile in the design: 475.93 × 302.33, centre 446px down (at 1440 wide)
	const TILE_WIDTH = 475.93;
	const TILE_HEIGHT = 302.33;
	const CENTRE_Y = 446;
	// At the start the main tile is centred this far down the screen (446 / 834 in the design)
	// and overfills the width slightly, so no edges show
	const START_CENTRE = 0.5348;
	const START_OVERFILL = 1.0135;
	// Share of the zoom over which the wordmark and text fade out
	const FADE_UNTIL = 0.35;

	function clamp(value) {
		return Math.max(0, Math.min(1, value));
	}

	// Slow start and finish
	function ease(p) {
		return p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2;
	}

	function init_zoom(section) {
		const stage = section.querySelector('.b-home-hero__stage');
		const mosaic = section.querySelector('.b-home-hero__mosaic');
		const main = section.querySelector('.b-home-hero__tile--main');
		const overlay = section.querySelector('.b-home-hero__overlay');
		const header = document.querySelector('.site-header');
		if (!stage || !mosaic || !main) return;

		let active = false;
		let distance = 1;
		let start_scale = 1;
		let start_shift = 0;
		let carry_header = false;
		let section_top = 0;
		let stick_top = 0;
		let frame = null;

		function reset() {
			section.classList.remove('is-zooming', 'is-zoomed-out');
			section.style.height = '';
			mosaic.style.transform = '';
			if (header) {
				header.style.position = '';
				header.style.transform = '';
			}
			if (overlay) {
				overlay.style.opacity = '';
				overlay.style.visibility = '';
			}
		}

		function measure() {
			reset();
			active = !reduced_motion.matches;
			if (!active) return;

			section.classList.add('is-zooming');

			// The stage sticks below the WordPress admin bar, when it's showing
			stick_top = parseFloat(getComputedStyle(stage).top) || 0;

			const width = stage.clientWidth;
			const screen = window.innerHeight - stick_top;
			const unit = main.offsetWidth / TILE_WIDTH;
			const tile_width = TILE_WIDTH * unit;
			const tile_height = TILE_HEIGHT * unit;

			// Scale at which the main tile covers the screen, and how far to move it to sit centred
			const centre = screen * START_CENTRE;
			start_scale = Math.max(width * START_OVERFILL / tile_width, (centre * 2) / tile_height);
			start_shift = centre - CENTRE_Y * unit;

			// One screen of scrolling to zoom out, while the stage sticks
			distance = screen;
			section.style.height = (stage.offsetHeight + distance) + 'px';

			// Only when the hero is at the top of the page, under the header (which, like
			// the hero, starts lower down when the WordPress admin bar is showing)
			section_top = section.getBoundingClientRect().top + window.scrollY;
			carry_header = !!header && section_top <= header.getBoundingClientRect().bottom + window.scrollY;

			update();
		}

		function update() {
			frame = null;
			if (!active) return;

			const scrolled = stick_top - section.getBoundingClientRect().top;
			const progress = clamp(scrolled / distance);
			const eased = ease(progress);

			// Scale evenly (geometrically), so the zoom doesn't rush at the start;
			// the tile moves up to its place as it shrinks
			const scale = Math.pow(start_scale, 1 - eased);
			const shift = start_scale > 1 ? start_shift * (scale - 1) / (start_scale - 1) : 0;
			mosaic.style.transform = 'translate3d(0, ' + shift.toFixed(2) + 'px, 0) scale(' + scale.toFixed(4) + ')';

			if (overlay) {
				const opacity = 1 - clamp(progress / FADE_UNTIL);
				overlay.style.opacity = opacity.toFixed(3);
				overlay.style.visibility = opacity > 0 ? '' : 'hidden';
			}

			section.classList.toggle('is-zoomed-out', progress > 0.3);

			// Keep the header where it sits on screen until the stage stops sticking. It's
			// fixed in place meanwhile (moving it with the script would lag a frame behind
			// the scrolling and jitter), then left where the stage lets go so it scrolls away with it.
			if (carry_header) {
				const release = section_top - stick_top + distance;
				if (window.scrollY < release) {
					header.style.position = 'fixed';
					header.style.transform = '';
				} else {
					header.style.position = '';
					header.style.transform = 'translate3d(0, ' + release.toFixed(1) + 'px, 0)';
				}
			}
		}

		function request_update() {
			if (!frame) frame = requestAnimationFrame(update);
		}

		let resize_timer = null;
		let last_width = window.innerWidth;
		let last_height = window.innerHeight;
		function request_measure() {
			// Phones change the screen height as the address bar shows and hides while
			// scrolling – re-measuring then would make the zoom jump, so it's skipped
			if (window.innerWidth === last_width && Math.abs(window.innerHeight - last_height) < 150) return;
			last_width = window.innerWidth;
			last_height = window.innerHeight;
			clearTimeout(resize_timer);
			resize_timer = setTimeout(measure, 150);
		}

		window.addEventListener('scroll', request_update, { passive: true });
		window.addEventListener('resize', request_measure);
		reduced_motion.addEventListener('change', measure);
		window.addEventListener('load', measure);

		measure();
	}

	/**
	 * Background videos – the main one, and the tiles' (loaded as they come near
	 * the screen). A main video set to start "once the page is scrolled" shows its
	 * photo at the top of the page: it loads and fades in when the visitor scrolls,
	 * and fades back to the photo (paused, to carry on later) if they scroll back up.
	 */
	function init_videos(section) {
		if (!window.zincBackgroundVideo) return;
		section.querySelectorAll('.b-home-hero__video').forEach(function (wrapper) {
			const options = { observe: wrapper.closest('.b-home-hero__tile') };
			if (wrapper.dataset.videoPlayback !== 'scroll') {
				window.zincBackgroundVideo(wrapper, options);
				return;
			}

			const scrolled = function () {
				return window.scrollY > 0;
			};
			options.is_active = scrolled;
			const video = window.zincBackgroundVideo(wrapper, options);

			let was_scrolled = scrolled();
			window.addEventListener('scroll', function () {
				if (scrolled() === was_scrolled) return;
				was_scrolled = !was_scrolled;
				video.refresh();
			}, { passive: true });
		});
	}

	// Tiles that open in the pop-up or follow a link – only once zoomed out to the grid
	const TILE = '.b-home-hero__tile[data-lightbox], a.b-home-hero__tile';

	function is_grid_showing(section) {
		return section.classList.contains('is-zoomed-out');
	}

	/**
	 * Pop-up <dialog> with the tile's video (with sound and controls) or its photo
	 * at full size. Closes with the button, Escape or a click on the dark area.
	 */
	function init_lightbox(section) {
		const modal = section.querySelector('.b-home-hero__modal');
		const tiles = section.querySelectorAll('.b-home-hero__tile[data-lightbox]');
		if (!modal || typeof modal.showModal !== 'function' || !tiles.length) return;

		const player = modal.querySelector('.b-home-hero__player');
		let opener = null;

		function open(tile) {
			const type = tile.dataset.lightbox;
			let media;
			if (type === 'embed') {
				media = document.createElement('iframe');
				media.src = tile.dataset.lightboxSrc;
				media.title = tile.dataset.lightboxLabel || '';
				media.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture; encrypted-media');
				media.setAttribute('allowfullscreen', '');
			} else if (type === 'video') {
				media = document.createElement('video');
				media.src = tile.dataset.lightboxSrc;
				media.controls = true;
				media.autoplay = true;
				media.playsInline = true;
			} else {
				media = document.createElement('img');
				media.src = tile.dataset.lightboxSrc;
				media.alt = tile.dataset.lightboxAlt || '';
			}

			opener = tile;
			player.classList.toggle('b-home-hero__player--image', type === 'image');
			player.replaceChildren(media);
			modal.setAttribute('aria-label', tile.dataset.lightboxLabel || '');
			modal.showModal();
		}

		// Removing the player stops the video
		modal.addEventListener('close', function () {
			player.replaceChildren();
			if (opener) opener.focus({ preventScroll: true });
		});
		modal.querySelector('.b-home-hero__close').addEventListener('click', function () {
			modal.close();
		});
		modal.addEventListener('click', function (e) {
			if (e.target === modal) modal.close();
		});

		// The tiles act as buttons while the grid is showing (not while the main
		// photo fills the screen); repeated tiles stay out of the keyboard order
		function update_tiles() {
			const showing = is_grid_showing(section);
			tiles.forEach(function (tile) {
				if (tile.hasAttribute('data-repeat')) return;
				if (showing) {
					tile.setAttribute('role', 'button');
					tile.setAttribute('tabindex', '0');
					tile.setAttribute('aria-haspopup', 'dialog');
					tile.setAttribute('aria-label', tile.dataset.lightboxLabel || '');
				} else {
					['role', 'tabindex', 'aria-haspopup', 'aria-label'].forEach(function (name) {
						tile.removeAttribute(name);
					});
				}
			});
		}
		update_tiles();
		new MutationObserver(update_tiles).observe(section, { attributes: true, attributeFilter: ['class'] });

		tiles.forEach(function (tile) {
			tile.addEventListener('click', function () {
				if (is_grid_showing(section)) open(tile);
			});
			tile.addEventListener('keydown', function (e) {
				if ((e.key === 'Enter' || e.key === ' ') && is_grid_showing(section)) {
					e.preventDefault();
					open(tile);
				}
			});
		});
	}

	/**
	 * "zoom" badge in place of the mouse pointer over the tiles, once zoomed out
	 * (assets/js/components/cursor-badge.js)
	 */
	function init_cursor(section) {
		const badge = window.zincCursorBadge && window.zincCursorBadge(section.querySelector('.c-cursor'));
		if (!badge) return;
		section.classList.add('has-cursor');

		let over = null;
		section.addEventListener('mouseover', function (e) {
			const tile = e.target.closest(TILE);
			if (tile === over) return;
			over = tile;
			if (tile && is_grid_showing(section)) badge.show(e);
			else badge.hide();
		});
		section.addEventListener('mouseleave', function () {
			over = null;
			badge.hide();
		});

		// Scrolling back up to the full-screen photo, or zooming out under a still mouse
		let last_x = 0, last_y = 0;
		section.addEventListener('mousemove', function (e) {
			last_x = e.clientX;
			last_y = e.clientY;
		});
		new MutationObserver(function () {
			if (!is_grid_showing(section)) {
				badge.hide();
			} else if (over) {
				badge.show({ clientX: last_x, clientY: last_y });
			}
		}).observe(section, { attributes: true, attributeFilter: ['class'] });
	}

	function setup() {
		document.querySelectorAll('.b-home-hero').forEach(function (section) {
			if (section.classList.contains('b-home-hero--grid')) init_zoom(section);
			init_videos(section);
			init_lightbox(section);
			init_cursor(section);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
