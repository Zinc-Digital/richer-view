/**
 * Accordion – one item open at a time; clicking the open item closes it.
 *
 * Each item needs a toggle <button> with aria-expanded. The open item gets the
 * "is-open" class (style the opening/closing in CSS).
 *
 * Usage:
 *   window.zincAccordion(section, {
 *     item: '.b-faq__item',
 *     toggle: '.b-faq__toggle',
 *     on_change: function (open_item) {},   // optional – open_item is null when all are closed
 *   });
 */
(function () {
	window.zincAccordion = function (root, options) {
		const items = Array.from(root.querySelectorAll(options.item));

		function set_open(item, open) {
			item.classList.toggle('is-open', open);
			item.querySelector(options.toggle).setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		items.forEach(function (item) {
			const toggle = item.querySelector(options.toggle);
			if (!toggle) return;

			toggle.addEventListener('click', function () {
				const opening = !item.classList.contains('is-open');
				items.forEach(function (other) {
					set_open(other, other === item && opening);
				});
				if (options.on_change) options.on_change(opening ? item : null);
			});
		});

		return items;
	};
})();
