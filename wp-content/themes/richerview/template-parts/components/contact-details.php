<?php
/**
 * Contact details – address, phone, email and social links from Theme Settings.
 *
 * Used in the site footer and the Contact block.
 * Styles: assets/sass/components/_c.contact-details.scss
 *
 * get_template_part('template-parts/components/contact-details', null, array(
 *     'align' => 'right', // 'left' (default) or 'right'
 * ));
 */

$align     = ($args['align'] ?? 'left') === 'right' ? 'right' : 'left';
$images    = get_template_directory_uri() . '/assets/images/figma';
$address   = get_field('address', 'option');
$telephone = get_field('telephone', 'option');
$email     = get_field('email', 'option');
$socials   = array(
	'instagram'	=> array('label' => 'Instagram',	'icon' => 'social-1.svg', 'height' => '33'),
	'facebook'	=> array('label' => 'Facebook',		'icon' => 'social-2.svg', 'height' => '33.0001'),
	'linkedin'	=> array('label' => 'LinkedIn',		'icon' => 'social-3.svg', 'height' => '33'),
);
?>

<div class="c-contact c-contact--<?php echo esc_attr($align); ?>">
  <?php if ($address) : ?>
  <p class="c-contact__address"><?php echo wp_kses($address, array('br' => array())); ?></p>
  <?php endif; ?>

  <?php if ($telephone) : ?>
  <a class="c-contact__link" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $telephone)); ?>">
    <img src="<?php echo esc_url($images . '/icon-phone.svg'); ?>" width="15" height="16" alt="" />
    <span><?php echo esc_html($telephone); ?></span>
  </a>
  <?php endif; ?>

  <?php if ($email) : ?>
  <a class="c-contact__link c-contact__link--email" href="mailto:<?php echo esc_attr(antispambot($email)); ?>">
    <img src="<?php echo esc_url($images . '/icon-email.svg'); ?>" width="17" height="14" alt="" />
    <span><?php echo esc_html(antispambot($email)); ?></span>
  </a>
  <?php endif; ?>

  <div class="c-contact__socials">
    <span class="c-contact__socials-label"><?php esc_html_e('the socials', 'zinc'); ?></span>
    <ul class="c-contact__socials-list">
      <?php foreach ($socials as $field => $social) :
				$url = get_field($field, 'option');
				if (!$url) continue; ?>
      <li>
        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($social['label']); ?>">
          <img src="<?php echo esc_url($images . '/' . $social['icon']); ?>" width="36" height="<?php echo esc_attr($social['height']); ?>" alt="" />
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
