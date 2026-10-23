<?php
/**
 * Contact block
 *
 * "Say hello" heading and intro, the contact details from Theme Settings, a map
 * image with a round badge, and a Gravity Form in a bordered card.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading      = get_field('heading');
$text         = get_field('text');
$show_details = get_field('show_details') !== false;
$map          = get_field('map_image');
$map_link     = get_field('map_link');
$badge        = get_field('badge');
// Field key (not name) so a brand new block picks up the default form before anything is saved
$form_id      = (int) get_field('field_6abfd25eae010');
$spacing      = get_field('spacing') === 'standard' ? 'standard' : 'page-top';
$level        = get_field('heading_level') === 'h2' ? 'h2' : 'h1';

$wrapper_attributes = array(
	'class' => 'b-contact' . ($spacing === 'page-top' ? ' b-contact--page-top' : ''),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}

// Use Gravity Forms' basic theme for this form – the block styles it to match the design
$basic_theme = function () {
	return 'gravity-theme';
};
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-contact__inner">
    <div class="b-contact__content">
      <?php if ($heading || $text) : ?>
      <div class="b-contact__intro">
        <?php if ($heading) : ?>
        <<?php echo $level; ?> class="b-contact__heading"><?php echo esc_html($heading); ?></<?php echo $level; ?>>
        <?php endif; ?>
        <?php if ($text) : ?>
        <p class="b-contact__text"><?php echo nl2br(esc_html($text), false); ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if ($show_details) {
				get_template_part('template-parts/components/contact-details', null, array('align' => 'left'));
			} ?>

      <?php if ($map) : ?>
      <div class="b-contact__map">
        <?php
					$map_image = wp_get_attachment_image($map, 'large', false, array(
						'class'		=> 'b-contact__map-image',
						'sizes'		=> '(min-width: 1025px) 464px, 100vw',
						'loading'	=> 'lazy',
					));
					if ($map_link) :
				?>
        <a class="b-contact__map-link" href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Open our location in Google Maps', 'zinc'); ?>"><?php echo $map_image; ?></a>
        <?php else : ?>
        <?php echo $map_image; ?>
        <?php endif; ?>

        <?php if ($badge) : ?>
        <p class="b-contact__badge" aria-hidden="true"><?php echo nl2br(esc_html($badge), false); ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="b-contact__form">
      <?php if ($form_id && function_exists('gravity_form')) :
				add_filter('gform_form_theme_slug', $basic_theme);
				gravity_form($form_id, false, false, false, null, true, 0, true);
				remove_filter('gform_form_theme_slug', $basic_theme);
			elseif ($is_preview) : ?>
      <p class="b-contact__placeholder"><?php esc_html_e('Choose a form in the block settings (Gravity Forms).', 'zinc'); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>
