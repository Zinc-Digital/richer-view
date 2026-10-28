</main>

<?php
	// The 404 page has its own legal links over its photo and no footer (404.php)
	if (!empty($args['minimal'])) :
		wp_footer();
		echo "</body>\n</html>\n";
		return;
	endif;

	$images    = get_template_directory_uri() . '/assets/images/figma';

	$contact_page = get_page_by_path('get-in-touch');
	$contact_url  = $contact_page ? get_permalink($contact_page) : home_url('/get-in-touch/');

	// Pages can hide the "get in touch" call to action (Page options > Hide footer call to action)
	$show_cta = !(is_singular() && get_field('hide_footer_cta', get_queried_object_id()));

	// …or give it their own wording and link (Page options > Custom call to action)
	$custom_cta = null;
	if ($show_cta && is_singular() && get_field('custom_cta', get_queried_object_id())) {
		$page_id    = get_queried_object_id();
		$cta_link   = get_field('cta_link', $page_id);
		$custom_cta = array(
			'heading'	=> (string) get_field('cta_heading', $page_id),
			'highlight'	=> trim((string) get_field('cta_highlight', $page_id)),
			'url'		=> $cta_link['url'] ?? '',
			'label'		=> $cta_link['title'] ?? '',
			'target'	=> $cta_link['target'] ?? '',
		);
		if ($custom_cta['heading'] === '') $custom_cta = null;
	}
?>

<footer class="site-footer<?php if (!$show_cta) echo ' site-footer--no-cta'; ?>">
  <?php if ($custom_cta) :
		// Underline the chosen words with the hand-drawn scribble (stretched to fit them)
		$cta_html = esc_html($custom_cta['heading']);
		if ($custom_cta['highlight'] !== '') {
			$cta_html = preg_replace(
				'/' . preg_quote(esc_html($custom_cta['highlight']), '/') . '/iu',
				'<span class="site-footer__cta-highlight site-footer__cta-highlight--fit">$0<img class="site-footer__cta-scribble" src="' . esc_url($images . '/cta-underline-fit.svg') . '" width="277" height="15.9999" alt="" /></span>',
				$cta_html,
				1
			);
		}
	?>
  <div class="site-footer__cta">
    <h2 class="site-footer__cta-title"><?php echo nl2br($cta_html, false); ?></h2>
    <?php if ($custom_cta['url']) : ?>
    <a class="site-footer__cta-link site-footer__cta-link--fit" href="<?php echo esc_url($custom_cta['url']); ?>"<?php if ($custom_cta['target']) : ?> target="<?php echo esc_attr($custom_cta['target']); ?>" rel="noopener"<?php endif; ?>>
      <span class="site-footer__cta-link-text">
        <?php echo esc_html($custom_cta['label'] ?: __('get in touch', 'zinc')); ?>
        <img class="site-footer__cta-arrow" src="<?php echo esc_url($images . '/arrow-large.svg'); ?>" width="29" height="20" alt="" />
      </span>
      <img class="site-footer__cta-underline" src="<?php echo esc_url($images . '/cta-link-underline-fit.svg'); ?>" width="321" height="6" alt="" />
    </a>
    <?php endif; ?>
  </div>
  <?php elseif ($show_cta) : ?>
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

  <?php get_template_part('template-parts/components/legal-links'); ?>

  <img class="site-footer__wordmark" src="<?php echo esc_url($images . '/footer-wordmark.svg'); ?>" width="1440" height="152.262" alt="" aria-hidden="true" />
</footer>

<?php wp_footer(); ?>
</body>
</html>
