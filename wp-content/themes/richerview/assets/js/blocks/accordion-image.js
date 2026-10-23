/**
 * Accordion with Image – one point open at a time (assets/js/components/accordion.js).
 *
 * Opening a point shows its text and sparkle, and swaps in its own photo or
 * video if it has one (otherwise the block's main photo or video shows).
 */
(function () {
	function init(section) {
		// Direct children only – the video layer has its own poster image inside it
		const images = Array.from(section.querySelectorAll('.b-accordion-image__media > .b-accordion-image__image'));

		function show_image(item) {
			const key = item && item.dataset.media;
			const has_own = key && images.some(function (img) { return img.dataset.media === key; });
			const target = has_own ? key : 'default';
			images.forEach(function (img) {
				img.classList.toggle('is-active', img.dataset.media === target);
			});
		}

		// Background videos – the main one and any point's own (assets/js/components/background-video.js).
		// Only the video in the layer currently shown is loaded and played.
		const media = section.querySelector('.b-accordion-image__media');
		const videos = window.zincBackgroundVideo
			? Array.from(section.querySelectorAll('.b-accordion-image__video')).map(function (video) {
				const layer = video.closest('.b-accordion-image__layer');
				return window.zincBackgroundVideo(video, {
					observe: media,
					is_active: function () {
						return layer.classList.contains('is-active');
					},
				});
			})
			: [];

		window.zincAccordion(section, {
			item: '.b-accordion-image__item',
			toggle: '.b-accordion-image__toggle',
			on_change: function (item) {
				show_image(item);
				videos.forEach(function (video) {
					video.refresh();
				});
			},
		});
	}

	function setup() {
		if (!window.zincAccordion) return;
		document.querySelectorAll('.b-accordion-image').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
