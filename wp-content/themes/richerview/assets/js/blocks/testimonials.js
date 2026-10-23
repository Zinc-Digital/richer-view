/**
 * Testimonials – draggable card row, "drag" / "Play" cursor badge and video pop-up.
 *
 * - The row starts centred, as in the design.
 * - With a mouse it can be dragged (with a little momentum when let go);
 *   touch screens swipe natively and keyboards use the arrow keys.
 * - Video cards open their video (with sound) in a pop-up <dialog>.
 */
(function () {
	// Draggable row (assets/js/components/drag-slider.js), starting centred as in the design.
	// Over a video card the badge shows the card's own text (e.g. "Play") instead of "‹ drag ›".
	function init_slider(section) {
		const row = section.querySelector('.b-testimonials__slider');
		if (!row || !window.zincDragSlider) return;

		const slider = window.zincDragSlider(section, row, { start: 'centre' });

		section.querySelectorAll('.b-testimonials__card--video[data-cursor-label]').forEach(function (card) {
			card.addEventListener('mouseenter', function (e) {
				if (!slider.is_dragging()) slider.show_label(e, card.dataset.cursorLabel);
			});
			card.addEventListener('mouseleave', function (e) {
				if (row.contains(e.relatedTarget)) slider.show_drag(e);
			});
		});
	}

	function init_videos(section) {
		const modal = section.querySelector('.b-testimonials__modal');
		if (!modal || typeof modal.showModal !== 'function') return;

		const player = modal.querySelector('.b-testimonials__player');
		let opener = null;

		function open(card) {
			let media;
			if (card.dataset.videoEmbed) {
				media = document.createElement('iframe');
				media.src = card.dataset.videoEmbed;
				media.title = card.getAttribute('aria-label') || '';
				media.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture; encrypted-media');
				media.setAttribute('allowfullscreen', '');
			} else if (card.dataset.videoSrc) {
				media = document.createElement('video');
				media.src = card.dataset.videoSrc;
				media.controls = true;
				media.autoplay = true;
				media.playsInline = true;
			} else {
				return;
			}

			opener = card;
			player.replaceChildren(media);
			modal.showModal();
		}

		// Removing the player stops the video
		modal.addEventListener('close', function () {
			player.replaceChildren();
			if (opener) opener.focus();
		});

		modal.querySelector('.b-testimonials__close').addEventListener('click', function () {
			modal.close();
		});

		// Clicking the dark area around the video closes it
		modal.addEventListener('click', function (e) {
			if (e.target === modal) modal.close();
		});

		section.querySelectorAll('.b-testimonials__card--video').forEach(function (card) {
			card.addEventListener('click', function () {
				open(card);
			});
		});
	}

	function setup() {
		document.querySelectorAll('.b-testimonials').forEach(function (section) {
			init_slider(section);
			init_videos(section);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
