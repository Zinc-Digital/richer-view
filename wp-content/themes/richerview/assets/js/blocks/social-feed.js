/**
 * Social Feed – a round "view" badge in place of the mouse pointer over each
 * post (assets/js/components/cursor-badge.js), and the feed plugin's
 * "Load More" button given the block's wording. Works for posts the plugin
 * adds later (Load More), as it listens on the whole feed.
 */
(function () {
	const POST = '.sbi_photo, .sbi_item a';

	function init(section) {
		const feed = section.querySelector('.b-feed__feed');
		if (!feed) return;

		// The block's "load more" wording, also after the plugin re-renders its button
		function label_button() {
			const text = feed.querySelector('.sbi_load_btn .sbi_btn_text');
			if (text && text.textContent !== section.dataset.loadMoreLabel) {
				text.textContent = section.dataset.loadMoreLabel;
			}
		}
		label_button();
		new MutationObserver(label_button).observe(feed, { childList: true, subtree: true });

		const badge = window.zincCursorBadge && window.zincCursorBadge(section.querySelector('.c-cursor'));
		if (!badge) return;
		section.classList.add('has-cursor');

		feed.addEventListener('mouseover', function (e) {
			if (e.target.closest(POST)) badge.show(e);
		});
		feed.addEventListener('mouseout', function (e) {
			const from = e.target.closest(POST);
			if (from && !from.contains(e.relatedTarget)) badge.hide();
		});
	}

	function setup() {
		document.querySelectorAll('.b-feed').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
