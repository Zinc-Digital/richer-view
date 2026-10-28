/**
 * Video Feature – clicking the image opens its video, with sound, in a pop-up <dialog>.
 * Closing the pop-up removes the player, which stops the video.
 */
(function () {
	function init(section) {
		const button = section.querySelector('.b-video-feature__play');
		const modal = section.querySelector('.b-video-feature__modal');
		if (!button || !modal || typeof modal.showModal !== 'function') return;

		const player = modal.querySelector('.b-video-feature__player');

		button.addEventListener('click', function () {
			let media;
			if (button.dataset.videoEmbed) {
				media = document.createElement('iframe');
				media.src = button.dataset.videoEmbed;
				media.title = button.getAttribute('aria-label') || '';
				media.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture; encrypted-media');
				media.setAttribute('allowfullscreen', '');
			} else if (button.dataset.videoSrc) {
				media = document.createElement('video');
				media.src = button.dataset.videoSrc;
				media.controls = true;
				media.autoplay = true;
				media.playsInline = true;
			} else {
				return;
			}
			player.replaceChildren(media);
			modal.showModal();
		});

		modal.addEventListener('close', function () {
			player.replaceChildren();
			button.focus();
		});

		modal.querySelector('.b-video-feature__close').addEventListener('click', function () {
			modal.close();
		});

		// Clicking the dark area around the video closes it
		modal.addEventListener('click', function (e) {
			if (e.target === modal) modal.close();
		});
	}

	function setup() {
		document.querySelectorAll('.b-video-feature').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
