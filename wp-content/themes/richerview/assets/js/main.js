window.addEventListener("load", function () {


		jQuery(".usp_box ul li").click(function () {
			jQuery(this).find('.info_box').slideToggle();
		});

		jQuery(".close").click(function () {
			jQuery(this).find('.info_box').hide();
		});


	/*** -- Scroll animation -- ***/
	function scrollInit() {
		//Display animted on scroll elements if they are within the viewport on load
		let window_bottom = jQuery(window).scrollTop() + jQuery(window).height();
		jQuery('.scroll-animated').each(function () {
			if (jQuery(this).offset().top <= window_bottom) {
				jQuery(this).addClass('animate');
			}
		});
	}
	scrollInit();

	jQuery(window).scroll(function (e) {
		//Animate in elements with the "scroll-animated" class
		let window_bottom = jQuery(window).scrollTop() + jQuery(window).height();

		jQuery('.scroll-animated').each(function () {
			if (jQuery(this).offset().top < window_bottom) {
				jQuery(this).addClass('animate');
			} else if (jQuery(this).offset().top > window_bottom) {
				jQuery(this).removeClass('animate');
			}
		});
	});
	/*** -- END Scroll animation -- ***/

	/*** -- Header menu -- ***/
	let site_header = document.querySelector(".site-header");
	if (site_header) {
		let menu_toggle = site_header.querySelector(".site-header__toggle");

		let set_menu = function (open) {
			site_header.classList.toggle("is-open", open);
			menu_toggle.setAttribute("aria-expanded", open);
			menu_toggle.setAttribute("aria-label", open ? menu_toggle.dataset.labelClose : menu_toggle.dataset.labelOpen);
		};

		menu_toggle.addEventListener("click", function () {
			set_menu(!site_header.classList.contains("is-open"));
		});

		document.addEventListener("keydown", function (e) {
			if (e.key === "Escape" && site_header.classList.contains("is-open")) {
				set_menu(false);
				menu_toggle.focus();
			}
		});
	}
	/*** -- END Header menu -- ***/


	/*** -- Read more -- ***/
	jQuery(".hidden-content").hide();
	jQuery(".show_hide").on("click", function () {
		jQuery(".show_hide").toggleClass('active');
		jQuery(this).parents('.body').find('.hidden-content').slideToggle(200);
	});


	/*** -- END Read more -- ***/
});