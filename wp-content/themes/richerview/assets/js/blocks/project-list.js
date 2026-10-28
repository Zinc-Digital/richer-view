/**
 * Project List – hovering or focusing a project shows its image. The image
 * stays when the mouse leaves the list; projects without an image leave the
 * current one showing.
 *
 * With category filters (the Our work page), choosing a category shows only
 * its projects. The choice is kept in the address (?category=film) so it can
 * be shared and survives going back from a project.
 */
(function () {
	function init(section) {
		const images = Array.from(section.querySelectorAll('.b-project-list__image'));
		const items = Array.from(section.querySelectorAll('.b-project-list__item'));

		function show(index) {
			const target = images.find(function (img) { return img.dataset.index === index; });
			if (!target) return;
			images.forEach(function (img) {
				img.classList.toggle('is-active', img === target);
			});
		}

		if (images.length > 1) {
			items.forEach(function (item) {
				const select = function () {
					show(item.dataset.index);
				};
				item.addEventListener('mouseenter', select);
				item.addEventListener('focusin', select);
			});

			// The hidden images are lazy-loaded – fetch them as the block comes near, so they're ready on hover
			const observer = new IntersectionObserver(function (entries) {
				if (!entries[0].isIntersecting) return;
				observer.disconnect();
				images.forEach(function (img) {
					img.loading = 'eager';
				});
			}, { rootMargin: '400px' });
			observer.observe(section);
		}

		init_filters(section, items, show);
	}

	function init_filters(section, items, show) {
		const buttons = Array.from(section.querySelectorAll('.b-project-list__filter'));
		if (!buttons.length) return;
		const empty = section.querySelector('.b-project-list__empty');

		function apply(category, update_address) {
			// An unknown category (e.g. an old link) shows everything
			if (!buttons.some(function (button) { return button.dataset.filter === category; })) category = '';

			buttons.forEach(function (button) {
				const active = button.dataset.filter === category;
				button.classList.toggle('is-active', active);
				button.setAttribute('aria-pressed', active ? 'true' : 'false');
			});

			const visible = items.filter(function (item) {
				const match = !category || item.dataset.categories.split(' ').indexOf(category) !== -1;
				item.hidden = !match;
				item.classList.remove('is-first-visible', 'is-last-visible');
				return match;
			});
			if (visible.length) {
				visible[0].classList.add('is-first-visible');
				visible[visible.length - 1].classList.add('is-last-visible');
				show(visible[0].dataset.index);
			}
			if (empty) empty.hidden = visible.length > 0;

			if (update_address) {
				const url = new URL(window.location.href);
				if (category) {
					url.searchParams.set('category', category);
				} else {
					url.searchParams.delete('category');
				}
				history.replaceState(history.state, '', url);
			}
		}

		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				apply(button.dataset.filter, true);
			});
		});

		const from_address = new URL(window.location.href).searchParams.get('category');
		if (from_address) apply(from_address, false);
	}

	function setup() {
		document.querySelectorAll('.b-project-list').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
