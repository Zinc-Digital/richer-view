<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php wp_head(); ?>

  <script src="https://kit.fontawesome.com/ef400fc0cd.js" crossorigin="anonymous"></script>

</head>

<body <?php body_class(); ?>>

  <?php
	$images    = get_template_directory_uri() . '/assets/images/figma';
	$telephone = get_field('telephone', 'option');
	$logo      = get_field('logo', 'option');
  ?>

  <header class="site-header">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-header__logo">
      <?php if ($logo) : ?>
      <img src="<?php echo esc_url($logo['url']); ?>" width="90" alt="<?php echo esc_attr($logo['alt'] ?: get_bloginfo('name')); ?>" />
      <?php else : ?>
      <img src="<?php echo esc_url($images . '/logo.svg'); ?>" width="90" height="33.6059" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
      <?php endif; ?>
    </a>

    <div class="site-header__actions">
      <nav class="site-header__nav" id="site-nav" aria-label="<?php esc_attr_e('Main menu', 'zinc'); ?>">
        <?php wp_nav_menu(array(
				'theme_location'	=> 'header',
				'container'			=> false,
				'menu_class'		=> 'site-header__menu',
				'depth'				=> 1,
				'fallback_cb'		=> false
			)); ?>
      </nav>

      <button class="site-header__button site-header__toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="<?php esc_attr_e('Open menu', 'zinc'); ?>" data-label-open="<?php esc_attr_e('Open menu', 'zinc'); ?>" data-label-close="<?php esc_attr_e('Close menu', 'zinc'); ?>">
        <img class="site-header__toggle-open" src="<?php echo esc_url($images . '/menu-toggle.svg'); ?>" width="41" height="41" alt="" />
        <img class="site-header__toggle-close" src="<?php echo esc_url($images . '/menu-close.svg'); ?>" width="41" height="41" alt="" />
      </button>

      <?php if ($telephone) : ?>
      <a class="site-header__button" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $telephone)); ?>" aria-label="<?php echo esc_attr(sprintf(__('Call us on %s', 'zinc'), $telephone)); ?>">
        <img src="<?php echo esc_url($images . '/phone-button.svg'); ?>" width="41" height="41" alt="" />
      </a>
      <?php endif; ?>
    </div>
  </header>
