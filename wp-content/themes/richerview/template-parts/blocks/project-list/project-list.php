<?php
/**
 * Project List block
 *
 * Heading and a list of projects (title, tag(s) and "view project" link)
 * beside an image. Hovering or focusing a project shows its image
 * (assets/js/blocks/project-list.js); the first project's image shows to start.
 *
 * Projects are either chosen by hand or listed from the Our Work post type.
 * The "Page listing" layout (the Our work page) makes the heading the page
 * title and adds category filters and column labels.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading    = get_field('heading');
$source     = get_field('field_6ad1f2a3b1020') === 'work' ? 'work' : 'manual';
$listing    = get_field('field_6ad1f2a3b1023') === 'listing';
$divider    = (bool) get_field('field_6ad1f2a3b1025');
$link_label = get_field('link_label') ?: __('view project', 'zinc');

$images = get_template_directory_uri() . '/assets/images/figma';

/**
 * Projects to list, in the same shape whichever source they come from:
 * title, tags (category => name), url, target, image (attachment ID).
 */
$items = array();
if ($source === 'work') {
	$args = array(
		'post_type'			=> 'work',
		'post_status'		=> 'publish',
		'posts_per_page'	=> (int) get_field('limit') ?: -1,
		'post__not_in'		=> is_singular('work') ? array(get_queried_object_id()) : array(),
		'no_found_rows'		=> true,
	);
	$categories = array_filter(array_map('intval', (array) get_field('categories')));
	if ($categories) {
		$args['tax_query'] = array(array('taxonomy' => 'work_category', 'terms' => $categories));
	}
	foreach (get_posts($args) as $post_item) {
		$terms = get_the_terms($post_item, 'work_category');
		$items[] = array(
			'title'		=> get_the_title($post_item),
			'tags'		=> $terms && !is_wp_error($terms) ? wp_list_pluck($terms, 'name', 'slug') : array(),
			'url'		=> get_permalink($post_item),
			'target'	=> '',
			'image'		=> get_post_thumbnail_id($post_item),
		);
	}
} else {
	foreach (get_field('projects') ?: array() as $project) {
		if (empty($project['title'])) continue;
		$tag = trim((string) ($project['tag'] ?? ''));
		$items[] = array(
			'title'		=> $project['title'],
			'tags'		=> $tag !== '' ? array(sanitize_title($tag) => $tag) : array(),
			'url'		=> $project['link']['url'] ?? '',
			'target'	=> $project['link']['target'] ?? '',
			'image'		=> $project['image'] ?? null,
		);
	}
}

// Category filters (Our Work projects on the listing layout): the categories these projects use, in category order
$filters = array();
if ($listing && $source === 'work' && get_field('field_6ad1f2a3b1024') !== false && $items) {
	$used = array();
	foreach ($items as $item) $used += $item['tags'];
	$terms = get_terms(array('taxonomy' => 'work_category', 'slug' => array_keys($used), 'orderby' => 'term_id', 'hide_empty' => false));
	if (!is_wp_error($terms)) {
		$filters = wp_list_pluck($terms, 'name', 'slug');
	}
}

// The image shown to start: the first project that has one
$first_image = null;
foreach ($items as $index => $item) {
	if (!empty($item['image'])) {
		$first_image = $index;
		break;
	}
}

$classes = 'b-project-list';
if ($listing) $classes .= ' b-project-list--listing';
if ($divider) $classes .= ' b-project-list--divider';

$wrapper_attributes = array(
	'class' => $classes,
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
$level = $listing ? 'h1' : 'h2';
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-project-list__inner">
    <?php if ($heading || $filters) : ?>
    <div class="b-project-list__header">
      <?php if ($heading) : ?>
      <<?php echo $level; ?> class="b-project-list__heading"><?php echo nl2br(esc_html($heading), false); ?></<?php echo $level; ?>>
      <?php endif; ?>

      <?php if ($filters) : ?>
      <div class="b-project-list__filters" role="group" aria-label="<?php esc_attr_e('Show projects by category', 'zinc'); ?>">
        <button type="button" class="b-project-list__filter is-active" data-filter="" aria-pressed="true"><?php echo esc_html(get_field('filter_all_label') ?: __('all', 'zinc')); ?></button>
        <?php foreach ($filters as $slug => $name) : ?>
        <button type="button" class="b-project-list__filter" data-filter="<?php echo esc_attr($slug); ?>" aria-pressed="false"><?php echo esc_html($name); ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($items) : ?>
    <div class="b-project-list__body">
      <?php if ($first_image !== null) : ?>
      <div class="b-project-list__media" aria-hidden="true">
        <?php foreach ($items as $index => $item) {
					if (empty($item['image'])) continue;
					echo wp_get_attachment_image($item['image'], 'large', false, array(
						'class'			=> 'b-project-list__image' . ($index === $first_image ? ' is-active' : ''),
						'sizes'			=> '(min-width: 1025px) 469px, 100vw',
						'loading'		=> 'lazy',
						'alt'			=> '',
						'data-index'	=> $index,
					));
				} ?>
      </div>
      <?php endif; ?>

      <div class="b-project-list__content">
        <?php if ($listing) : ?>
        <div class="b-project-list__labels" aria-hidden="true">
          <span class="b-project-list__label"><?php echo esc_html(get_field('project_label') ?: __('project', 'zinc')); ?></span>
          <span class="b-project-list__label"><?php echo esc_html(get_field('services_label') ?: __('service(s)', 'zinc')); ?></span>
        </div>
        <?php endif; ?>

        <ul class="b-project-list__list">
          <?php foreach ($items as $index => $item) : ?>
          <li class="b-project-list__item" data-index="<?php echo esc_attr($index); ?>" data-categories="<?php echo esc_attr(implode(' ', array_keys($item['tags']))); ?>">
            <h3 class="b-project-list__title"><?php echo esc_html($item['title']); ?></h3>

            <div class="b-project-list__meta">
              <?php if ($item['tags']) : ?>
              <ul class="b-project-list__tags">
                <?php foreach ($item['tags'] as $tag) : ?>
                <li class="b-project-list__tag"><?php echo esc_html($tag); ?></li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>

              <?php if ($item['url']) : ?>
              <a class="b-project-list__link" href="<?php echo esc_url($item['url']); ?>"<?php if ($item['target']) : ?> target="<?php echo esc_attr($item['target']); ?>" rel="noopener"<?php endif; ?> aria-label="<?php echo esc_attr(sprintf(__('%1$s: %2$s', 'zinc'), $link_label, $item['title'])); ?>">
                <span class="b-project-list__link-text">
                  <?php echo esc_html($link_label); ?>
                  <span class="b-project-list__arrow">
                    <img src="<?php echo esc_url($images . '/project-arrow.svg'); ?>" width="12" height="9" alt="" />
                    <img class="b-project-list__hover-icon" src="<?php echo esc_url($images . '/project-arrow-hover.svg'); ?>" width="12" height="9" alt="" />
                  </span>
                </span>
                <span class="b-project-list__underline">
                  <img src="<?php echo esc_url($images . '/project-underline.svg'); ?>" width="106" height="4" alt="" />
                  <img class="b-project-list__hover-icon" src="<?php echo esc_url($images . '/project-underline-hover.svg'); ?>" width="106" height="4" alt="" />
                </span>
              </a>
              <?php endif; ?>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>

        <?php if ($filters) : ?>
        <p class="b-project-list__empty" hidden><?php esc_html_e('No projects in this category yet.', 'zinc'); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php elseif ($is_preview) : ?>
    <p class="b-project-list__placeholder"><?php echo $source === 'work' ? esc_html__('No published projects to show yet – add some under Our Work.', 'zinc') : esc_html__('Add projects in the block settings.', 'zinc'); ?></p>
    <?php endif; ?>
  </div>
</section>
