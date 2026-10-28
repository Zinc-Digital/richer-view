/**
 * Social Feed – a round "view" badge in place of the mouse pointer over each
 * post (assets/js/components/cursor-badge.js), and the feed plugin's
 * "Load More" button given the block's wording. Works for posts the plugin
 * adds later (Load More), as it listens on the whole feed.
 *
 * The Zinc Instagram Feed mu-plugin has no "load more" of its own, so the
 * block shows its first three rows and adds one that shows three more a click.
 */
(function () {
	const POST = '.sbi_photo, .sbi_item a, .zig-card';
	const ROWS = 3;

	function init_load_more(section, feed) {
		const track = feed.querySelector('.zig-track');
		if (!track) return;
		const cards = Array.from(track.querySelectorAll('.zig-card'));

		const wrap = document.createElement('div');
		wrap.className = 'b-feed__more';
		wrap.innerHTML = '<button type="button" class="b-feed__more-btn"></button>';
		const button = wrap.firstChild;
		button.textContent = section.dataset.loadMoreLabel;
		feed.appendChild(wrap);

		let shown = 0;

		function columns() {
			return getComputedStyle(track).gridTemplateColumns.split(' ').length || 3;
		}

		function show(count) {
			shown = Math.min(cards.length, count);
			cards.forEach(function (card, i) {
				card.classList.toggle('is-hidden', i >= shown);
			});
			wrap.hidden = shown >= cards.length;
		}

		button.addEventListener('click', function () {
			const first = cards[shown];
			show(shown + columns() * ROWS);
			if (first) first.focus({ preventScroll: true });
		});

		show(columns() * ROWS);
	}

	function init(section) {
		const feed = section.querySelector('.b-feed__feed');
		if (!feed) return;

		init_load_more(section, feed);

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
