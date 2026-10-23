/**
 * Background video – a muted, looping YouTube/Vimeo/uploaded video that fills
 * its box (see inc/video-helpers.php for the embed URLs).
 *
 * Markup: a wrapper with either data-video-embed="…" (YouTube/Vimeo) or a
 * <video> inside it (uploaded file, optionally with `autoplay` so it starts
 * before this script runs). The wrapper gets "is-playing" once the video is
 * ready, for blocks that fade it in over a poster image.
 *
 * Players are loaded the first time the box comes near the screen, then paused
 * while it's off screen (or hidden, e.g. a closed accordion point) and resumed
 * when it's back – so the video carries on looping rather than restarting.
 * Nothing plays for visitors who prefer reduced motion.
 *
 * Usage:
 *   const video = window.zincBackgroundVideo(wrapper, {
 *     observe: element,      // optional – element to watch for scrolling into view (default: wrapper)
 *     hover_target: element, // optional – only show while the mouse is over this element (mouse only)
 *     is_active: function,   // optional – return false while the video is hidden (e.g. a closed
 *                            //            accordion point); it then isn't loaded or played
 *   });
 *   video.refresh();         // call after whatever is_active depends on changes
 */
(function () {
	const reduced_motion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const can_hover = window.matchMedia('(hover: hover) and (pointer: fine)');

	// Give embedded players a moment to get past their loading screen before fading in
	const EMBED_FADE_DELAY = 800;

	window.zincBackgroundVideo = function (wrapper, options) {
		const api = { refresh: function () {} };
		if (!wrapper) return api;

		// Reduced motion: no playback – an uploaded video stops on its first frame
		if (reduced_motion.matches) {
			const still = wrapper.querySelector('video');
			if (still) {
				still.removeAttribute('autoplay');
				still.pause();
				still.currentTime = 0;
			}
			return api;
		}

		options = options || {};
		const embed_url = wrapper.dataset.videoEmbed;
		const file = wrapper.querySelector('video');
		const hover_target = options.hover_target && can_hover.matches ? options.hover_target : null;
		const is_active = typeof options.is_active === 'function' ? options.is_active : function () { return true; };
		let ready = false;
		let hovered = false;
		let in_view = false;
		let fade_timer = null;

		// Tell a YouTube/Vimeo player to play or pause (YouTube needs enablejsapi=1 in its URL)
		function command(name) {
			const iframe = wrapper.querySelector('iframe');
			if (!iframe || !iframe.contentWindow) return;
			const message = /vimeo\.com/.test(embed_url)
				? { method: name }
				: { event: 'command', func: name === 'play' ? 'playVideo' : 'pauseVideo', args: [] };
			iframe.contentWindow.postMessage(JSON.stringify(message), '*');
		}

		// Show when ready (and hovered if hover mode is on); uploads only play while on screen
		function update() {
			const show = ready && is_active() && (!hover_target || hovered);
			wrapper.classList.toggle('is-playing', show);

			if (file) {
				if (show && in_view) {
					const playing = file.play();
					if (playing) playing.catch(function () {});
				} else {
					file.pause();
				}
			}
		}

		function load() {
			if (embed_url) {
				// Already in the page (output by the template so it loads with the page)
				if (wrapper.querySelector('iframe')) {
					ready = true;
					return;
				}
				const iframe = document.createElement('iframe');
				iframe.src = embed_url;
				iframe.title = '';
				iframe.tabIndex = -1;
				iframe.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture; encrypted-media');
				iframe.addEventListener('load', function () {
					fade_timer = setTimeout(function () {
						ready = true;
						update();
					}, EMBED_FADE_DELAY);
				});
				wrapper.appendChild(iframe);
			} else if (file) {
				ready = true;
				update();
			}
		}

		if (hover_target) {
			const set_hovered = function (value) {
				return function () {
					hovered = value;
					update();
				};
			};
			hover_target.addEventListener('mouseenter', set_hovered(true));
			hover_target.addEventListener('mouseleave', set_hovered(false));
			hover_target.addEventListener('focusin', set_hovered(true));
			hover_target.addEventListener('focusout', set_hovered(false));
		}

		// Load the first time it's needed; after that pause and resume (no reload, no restart)
		api.refresh = function () {
			const needed = in_view && is_active();
			if (needed) {
				const loaded = !!wrapper.querySelector('iframe') || (file && ready);
				load();
				if (loaded && embed_url) command('play');
			} else if (embed_url) {
				command('pause');
			}
			update();
		};

		new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				in_view = entry.isIntersecting;
				api.refresh();
			});
		}, { rootMargin: '200px' }).observe(options.observe || wrapper);

		return api;
	};
})();
