/**
 * Image Gallery – draggable row with the "‹ drag ›" cursor badge
 * (assets/js/components/drag-slider.js), starting centred or at the start
 * depending on the block's layout.
 */
(function () {
	function setup() {
		if (!window.zincDragSlider) return;
		document.querySelectorAll('.b-gallery').forEach(function (section) {
			window.zincDragSlider(section, section.querySelector('.b-gallery__slider'), { start: section.dataset.start || 'centre' });
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
