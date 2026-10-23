<?php
/**
 * Video helpers
 *
 * Turn YouTube / Vimeo links into muted, looping "background" embed URLs.
 */

/**
 * Get the video ID (and Vimeo private hash) from a YouTube or Vimeo URL.
 *
 * @param string $url  Link pasted by the editor.
 * @param string $type 'youtube' or 'vimeo'.
 * @return array|null  array('id' => '', 'hash' => '') or null if not recognised.
 */
function zinc_parse_video_url($url, $type) {
	$url = trim((string) $url);
	if ($url === '') return null;

	if ($type === 'youtube') {
		// youtu.be/ID, youtube.com/watch?v=ID, /embed/ID, /shorts/ID, /live/ID
		if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $url, $match)) {
			return array('id' => $match[1], 'hash' => '');
		}
	}

	if ($type === 'vimeo') {
		// vimeo.com/ID, vimeo.com/ID/HASH (unlisted), player.vimeo.com/video/ID?h=HASH
		if (preg_match('~vimeo\.com/(?:.*?/)?(?:video/)?(\d+)(?:/([a-f0-9]+))?~i', $url, $match)) {
			$hash = $match[2] ?? '';
			if (!$hash && preg_match('~[?&]h=([a-f0-9]+)~i', $url, $hash_match)) {
				$hash = $hash_match[1];
			}
			return array('id' => $match[1], 'hash' => $hash);
		}
	}

	return null;
}

/**
 * Work out which service a link belongs to. Uses the chosen type if the link
 * matches it, otherwise tries the other one – so a Vimeo link still works if
 * "YouTube" was picked by mistake (and vice versa).
 *
 * @param string $url  Link pasted by the editor.
 * @param string $type 'youtube' or 'vimeo' as chosen in the block.
 * @return array array($type, $video) – $video is null if neither service recognises the link.
 */
function zinc_detect_video($url, $type) {
	$types = $type === 'vimeo' ? array('vimeo', 'youtube') : array('youtube', 'vimeo');
	foreach ($types as $try) {
		$video = zinc_parse_video_url($url, $try);
		if ($video) {
			return array($try, $video);
		}
	}
	return array($type, null);
}

/**
 * Build a muted, looping, controls-free embed URL for use as a background video.
 *
 * @param string $url  Link pasted by the editor.
 * @param string $type 'youtube' or 'vimeo'.
 * @return string Embed URL, or '' if the link isn't recognised.
 */
function zinc_get_background_video_embed_url($url, $type) {
	list($type, $video) = zinc_detect_video($url, $type);
	if (!$video) return '';

	if ($type === 'youtube') {
		return add_query_arg(array(
			'autoplay'			=> 1,
			'mute'				=> 1,
			'loop'				=> 1,
			'playlist'			=> $video['id'],
			'controls'			=> 0,
			'disablekb'			=> 1,
			'playsinline'		=> 1,
			'rel'				=> 0,
			'iv_load_policy'	=> 3,
			'enablejsapi'		=> 1, // lets background-video.js pause/resume it
		), 'https://www.youtube-nocookie.com/embed/' . $video['id']);
	}

	$args = array(
		'background'	=> 1,
		'autoplay'		=> 1,
		'loop'			=> 1,
		'muted'			=> 1,
		'dnt'			=> 1,
	);
	if ($video['hash']) {
		$args['h'] = $video['hash'];
	}
	return add_query_arg($args, 'https://player.vimeo.com/video/' . $video['id']);
}

/**
 * Build an embed URL for normal playback (sound and controls on), starting
 * straight away – e.g. for a video opened in a pop-up.
 *
 * @param string $url  Link pasted by the editor.
 * @param string $type 'youtube' or 'vimeo'.
 * @return string Embed URL, or '' if the link isn't recognised.
 */
function zinc_get_video_embed_url($url, $type) {
	list($type, $video) = zinc_detect_video($url, $type);
	if (!$video) return '';

	if ($type === 'youtube') {
		return add_query_arg(array(
			'autoplay'		=> 1,
			'playsinline'	=> 1,
			'rel'			=> 0,
		), 'https://www.youtube-nocookie.com/embed/' . $video['id']);
	}

	$args = array(
		'autoplay'	=> 1,
		'dnt'		=> 1,
	);
	if ($video['hash']) {
		$args['h'] = $video['hash'];
	}
	return add_query_arg($args, 'https://player.vimeo.com/video/' . $video['id']);
}

/**
 * Check a direct video file link (e.g. a Vimeo video file link or a file on
 * other hosting) so it can be played with a plain <video> element.
 *
 * @param string $url Link pasted by the editor.
 * @return string The link, or '' if it isn't an http(s) address.
 */
function zinc_get_video_file_link($url) {
	$url = trim((string) $url);
	return preg_match('~^https?://~i', $url) ? esc_url_raw($url) : '';
}

/**
 * Output a background video player <iframe> straight into the page, so a video
 * that's visible when the page opens starts loading immediately instead of
 * waiting for JavaScript (assets/js/components/background-video.js reuses it).
 *
 * @param string $embed_url Embed URL from zinc_get_background_video_embed_url().
 * @param string $loading   'eager', or 'lazy' to let the browser wait until it's near the screen
 *                          (for videos further down the page, so they don't hold up one at the top).
 */
function zinc_background_video_iframe($embed_url, $loading = 'eager') {
	if (!$embed_url) return;
	printf(
		'<iframe src="%s" title="" tabindex="-1" loading="%s" allow="autoplay; fullscreen; picture-in-picture; encrypted-media"></iframe>',
		esc_url($embed_url),
		$loading === 'lazy' ? 'lazy' : 'eager'
	);
}

/**
 * Connect to the video player's servers early on pages that embed a YouTube or
 * Vimeo video in one of the theme's blocks.
 */
function zinc_video_resource_hints($urls, $relation_type) {
	if ($relation_type !== 'preconnect' || !is_singular()) return $urls;

	$content = (string) get_post_field('post_content', get_queried_object_id());
	$hosts   = array();
	if (strpos($content, 'vimeo.com') !== false) {
		$hosts = array_merge($hosts, array('https://player.vimeo.com', 'https://i.vimeocdn.com', 'https://f.vimeocdn.com', 'https://vod-adaptive-ak.vimeocdn.com'));
	}
	if (strpos($content, 'youtube.com') !== false || strpos($content, 'youtu.be') !== false) {
		$hosts = array_merge($hosts, array('https://www.youtube-nocookie.com', 'https://i.ytimg.com', 'https://www.google.com'));
	}
	foreach ($hosts as $host) {
		$urls[] = array('href' => $host, 'crossorigin');
	}
	return $urls;
}
add_filter('wp_resource_hints', 'zinc_video_resource_hints', 10, 2);
