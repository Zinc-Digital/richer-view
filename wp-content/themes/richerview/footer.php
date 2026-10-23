</main>

<?php
	$images    = get_template_directory_uri() . '/assets/images/figma';

	$contact_page = get_page_by_path('get-in-touch');
	$contact_url  = $contact_page ? get_permalink($contact_page) : home_url('/get-in-touch/');

	// Pages can hide the "get in touch" call to action (Page options > Hide footer call to action)
	$show_cta = !(is_singular() && get_field('hide_footer_cta', get_queried_object_id()));
?>

<footer class="site-footer<?php if (!$show_cta) echo ' site-footer--no-cta'; ?>">
  <?php if ($show_cta) : ?>
  <div class="site-footer__cta">
    <h2 class="site-footer__cta-title">
      starting a <span class="site-footer__cta-highlight">new project<img class="site-footer__cta-scribble" src="<?php echo esc_url($images . '/new-project-underline.svg'); ?>" width="405" height="25.9998" alt="" /></span><br>
      or want to chat with us?
    </h2>
    <a class="site-footer__cta-link" href="<?php echo esc_url($contact_url); ?>">
      <span class="site-footer__cta-link-text">
        get in touch
        <img class="site-footer__cta-arrow" src="<?php echo esc_url($images . '/arrow-large.svg'); ?>" width="29" height="20" alt="" />
      </span>
      <img class="site-footer__cta-underline" src="<?php echo esc_url($images . '/get-in-touch-underline.svg'); ?>" width="284" height="6" alt="" />
    </a>
  </div>
  <?php endif; ?>

  <img class="site-footer__divider" src="<?php echo esc_url($images . '/divider-line.svg'); ?>" width="1440" height="1" alt="" />

  <div class="site-footer__main">
    <div class="site-footer__navs">
      <?php foreach (array('footer_nav_1', 'footer_nav_2') as $location) :
				wp_nav_menu(array(
					'theme_location'	=> $location,
					'container'			=> 'nav',
					'container_class'	=> 'site-footer__nav',
					'menu_class'		=> 'site-footer__menu',
					'depth'				=> 1,
					'fallback_cb'		=> false
				));
			endforeach; ?>
    </div>

    <div class="site-footer__contact">
      <?php get_template_part('template-parts/components/contact-details', null, array('align' => 'right')); ?>
    </div>
  </div>

  <ul class="site-footer__legal">
    <li>&copy; richer view media productions</li>
    <?php
			$legal_items = has_nav_menu('footer_legal') ? wp_get_nav_menu_items(get_nav_menu_locations()['footer_legal']) : array();
			foreach ((array) $legal_items as $item) : ?>
    <li><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
    <?php endforeach; ?>
    <li><a class="site-footer__credit" href="https://zinc.digital/" target="_blank" rel="noopener">website by zinc digital</a></li>
  </ul>

  <img class="site-footer__wordmark" src="<?php echo esc_url($images . '/footer-wordmark.svg'); ?>" width="1440" height="152.262" alt="" aria-hidden="true" />
</footer>

<?php wp_footer(); ?>
</body>
</html>
