/**
 * Image Gallery – draggable row of images and videos with the "‹ drag ›" cursor badge
 * (assets/js/components/drag-slider.js), starting centred or at the start
 * depending on the block's layout.
 */
(function () {
	// Videos (assets/js/components/background-video.js): "Play on hover" ones only show
	// while the mouse is over them, like the Services Showcase cards
	function init_videos(section) {
		if (!window.zincBackgroundVideo) return;
		section.querySelectorAll('.b-gallery__video').forEach(function (wrapper) {
			const item = wrapper.closest('.b-gallery__item');
			window.zincBackgroundVideo(wrapper, {
				observe: item,
				hover_target: wrapper.dataset.videoPlayback === 'hover' ? item : null,
			});
		});
	}

	function setup() {
		document.querySelectorAll('.b-gallery').forEach(function (section) {
			if (window.zincDragSlider) {
				window.zincDragSlider(section, section.querySelector('.b-gallery__slider'), { start: section.dataset.start || 'centre' });
			}
			init_videos(section);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
