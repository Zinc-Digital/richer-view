/**
 * Vacancy List – one role open at a time (assets/js/components/accordion.js),
 * with a round badge in place of the mouse pointer over the roles
 * (assets/js/components/cursor-badge.js): "open" over a closed role, "close"
 * over the open one. A link to #role-{name} opens that role.
 */
(function () {
	function init(section) {
		let hovered = null;
		let last_event = null;

		const items = window.zincAccordion(section, {
			item: '.b-vacancies__item',
			toggle: '.b-vacancies__toggle',
			on_change: function () {
				if (hovered) show_badge(hovered);
			},
		});

		// Opened from a link to this role
		if (window.location.hash) {
			const linked = items.find(function (item) { return '#' + item.id === window.location.hash; });
			if (linked) linked.querySelector('.b-vacancies__toggle').click();
		}

		const badge = window.zincCursorBadge && window.zincCursorBadge(section.querySelector('.c-cursor'));
		if (!badge) return;

		function show_badge(item, e) {
			if (e) last_event = e;
			if (!last_event) return;
			const label = document.createElement('span');
			label.className = 'c-cursor__label';
			label.textContent = item.classList.contains('is-open') ? section.dataset.closeLabel : section.dataset.openLabel;
			badge.show(last_event, label);
		}

		section.classList.add('has-cursor');
		items.forEach(function (item) {
			const toggle = item.querySelector('.b-vacancies__toggle');
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
		document.querySelectorAll('.b-vacancies').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
