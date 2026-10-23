/**
 * Intro with Image – plays the optional background video over the image
 * (assets/js/components/background-video.js).
 */
(function () {
	function setup() {
		if (!window.zincBackgroundVideo) return;
		document.querySelectorAll('.b-intro-image__video').forEach(function (video) {
			window.zincBackgroundVideo(video, { observe: video.closest('.b-intro-image__media') });
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
