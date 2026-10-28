<?php
/**
 * Vacancy List block
 *
 * The open roles from the Vacancies post type (soonest closing first), as rows
 * of title, type and closing date. Clicking a role opens its summary and an
 * apply link; a round badge ("open" / "close") replaces the mouse pointer over
 * the roles (assets/js/blocks/vacancy-list.js). Roles leave the list after
 * their closing date (inc/vacancies.php).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading     = get_field('heading');
$apply_label = get_field('apply_label') ?: __('apply for this role', 'zinc');
$ongoing     = get_field('ongoing_label') ?: __('ongoing', 'zinc');
$empty_text  = get_field('empty_text');
$vacancies   = zinc_get_vacancies();

$images = get_template_directory_uri() . '/assets/images/figma';
$id     = 'roles-' . substr(md5($block['id'] ?? uniqid()), 0, 8);

$wrapper_attributes = array(
	'class'				=> 'b-vacancies',
	'data-open-label'	=> get_field('open_label') ?: __('open', 'zinc'),
	'data-close-label'	=> get_field('close_label') ?: __('close', 'zinc'),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-vacancies__inner">
    <?php if ($heading) : ?>
    <h2 class="b-vacancies__heading"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>

    <?php if ($vacancies) : ?>
    <div class="b-vacancies__labels" aria-hidden="true">
      <span class="b-vacancies__label"><?php echo esc_html(get_field('role_label') ?: __('role', 'zinc')); ?></span>
      <span class="b-vacancies__label-group">
        <span class="b-vacancies__label"><?php echo esc_html(get_field('type_label') ?: __('type', 'zinc')); ?></span>
        <span class="b-vacancies__label"><?php echo esc_html(get_field('closes_label') ?: __('closes', 'zinc')); ?></span>
      </span>
    </div>

    <ul class="b-vacancies__list">
      <?php foreach ($vacancies as $vacancy) :
				$item_id = $id . '-' . $vacancy->ID;
				$type    = get_field('type', $vacancy->ID);
				$closes  = zinc_format_closing_date(get_field('closing_date', $vacancy->ID)) ?: $ongoing;
				$summary = get_field('summary', $vacancy->ID);
				$link    = get_field('apply_link', $vacancy->ID);
				$url     = $link['url'] ?? get_permalink($vacancy) . '#apply';
				$target  = $link['target'] ?? '';
			?>
      <li class="b-vacancies__item" id="<?php echo esc_attr('role-' . $vacancy->post_name); ?>">
        <h3 class="b-vacancies__role">
          <button type="button" class="b-vacancies__toggle" id="<?php echo esc_attr($item_id); ?>-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr($item_id); ?>-panel">
            <span class="b-vacancies__title"><?php echo esc_html(get_the_title($vacancy)); ?></span>
            <span class="b-vacancies__meta">
              <?php if ($type) : ?>
              <span class="b-vacancies__type"><?php echo esc_html($type); ?></span>
              <?php endif; ?>
              <span class="b-vacancies__closes"><span class="screen-reader-text"><?php esc_html_e('Closes:', 'zinc'); ?> </span><?php echo esc_html($closes); ?></span>
            </span>
          </button>
        </h3>
        <div class="b-vacancies__panel" id="<?php echo esc_attr($item_id); ?>-panel" role="region" aria-labelledby="<?php echo esc_attr($item_id); ?>-toggle">
          <div class="b-vacancies__panel-inner">
            <?php if ($summary) : ?>
            <p class="b-vacancies__summary"><?php echo nl2br(esc_html($summary), false); ?></p>
            <?php endif; ?>
            <a class="b-vacancies__apply" href="<?php echo esc_url($url); ?>"<?php if ($target) : ?> target="<?php echo esc_attr($target); ?>" rel="noopener"<?php endif; ?>>
              <span class="b-vacancies__apply-text">
                <?php echo esc_html($apply_label); ?>
                <img src="<?php echo esc_url($images . '/project-arrow-hover.svg'); ?>" width="12" height="9" alt="" />
              </span>
              <img class="b-vacancies__apply-underline" src="<?php echo esc_url($images . '/apply-underline.svg'); ?>" width="145" height="4" alt="" />
            </a>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php elseif ($empty_text || $is_preview) : ?>
    <p class="b-vacancies__empty"><?php echo esc_html($empty_text ?: __('No open roles to show – add some under Vacancies.', 'zinc')); ?></p>
    <?php endif; ?>
  </div>

  <?php if ($vacancies) : ?>
  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge"></div>
  </div>
  <?php endif; ?>
</section>
