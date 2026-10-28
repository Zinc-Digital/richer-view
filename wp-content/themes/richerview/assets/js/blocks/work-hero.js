/**
 * Work Hero – plays the optional background video (assets/js/components/background-video.js).
 */
(function () {
	function setup() {
		if (!window.zincBackgroundVideo) return;
		document.querySelectorAll('.b-work-hero__video').forEach(function (video) {
			window.zincBackgroundVideo(video, { observe: video.closest('.b-work-hero') });
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
