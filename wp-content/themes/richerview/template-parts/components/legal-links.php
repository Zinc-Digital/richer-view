<?php
/**
 * Legal links – copyright, the "Footer legal" menu and the Zinc credit.
 * Used at the bottom of the footer and on the 404 page.
 *
 * @var array $args 'class' – class for the list (its items are styled through it).
 */

$class = $args['class'] ?? 'site-footer__legal';
?>
<ul class="<?php echo esc_attr($class); ?>">
  <li>&copy; richer view media productions</li>
  <?php
	$legal_items = has_nav_menu('footer_legal') ? wp_get_nav_menu_items(get_nav_menu_locations()['footer_legal']) : array();
	foreach ((array) $legal_items as $item) : ?>
  <li><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
  <?php endforeach; ?>
  <li><a class="site-footer__credit" href="https://zinc.digital/" target="_blank" rel="noopener">website by zinc digital</a></li>
</ul>
