/**
 * FAQ – one question open at a time (assets/js/components/accordion.js), with
 * a round badge in place of the mouse pointer over the questions
 * (assets/js/components/cursor-badge.js): "open" over a closed question,
 * "close" over the open one.
 */
(function () {
	function init(section) {
		const items = window.zincAccordion(section, {
			item: '.b-faq__item',
			toggle: '.b-faq__toggle',
			on_change: function () {
				if (hovered) show_badge(hovered);
			},
		});

		const badge = window.zincCursorBadge && window.zincCursorBadge(section.querySelector('.c-cursor'));
		let hovered = null;
		let last_event = null;
		if (!badge) return;

		function show_badge(item, e) {
			if (e) last_event = e;
			const label = document.createElement('span');
			label.className = 'c-cursor__label';
			label.textContent = item.classList.contains('is-open') ? section.dataset.closeLabel : section.dataset.openLabel;
			badge.show(last_event, label);
		}

		section.classList.add('has-cursor');
		items.forEach(function (item) {
			const toggle = item.querySelector('.b-faq__toggle');
			toggle.addEventListener('mouseenter', function (e) {
				hovered = item;
				show_badge(item, e);
			});
			toggle.addEventListener('mousemove', function (e) {
				last_event = e;
			});
			toggle.addEventListener('mouseleave', function () {
				hovered = null;
				badge.hide();
			});
		});
	}

	function setup() {
		if (!window.zincAccordion) return;
		document.querySelectorAll('.b-faq').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
