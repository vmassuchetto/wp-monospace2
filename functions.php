<?php

define ('MONOSPACE_CREDITS_URL', 'http://vinicius.soylocoporti.org.br/monospace2-wordpress-theme/');
define ('MONOSPACE_DEFAULT_ICON', 'document&');

load_theme_textdomain ('monospace2', get_template_directory().'/lang');

require_once(get_stylesheet_directory() . '/options.php');
require_once(get_stylesheet_directory() . '/widgets.php');

register_nav_menu('header', __('Header Menu', 'monospace2'));

register_sidebar(array(
	'name'          => __('Expandable Menu', 'monospace2'),
	'id'            => 'expandable-menu',
	'description'   => '',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '',
	'after_title'   => ''
));

global $content_width;
if (!isset($content_width))
    $content_width = 690;

add_theme_support('automatic-feed-links');

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
add_filter('the_content', 'monospace_wpautop');
add_filter('the_content', 'monospace_wpautop');
function monospace_wpautop($content) {
    return wpautop($content, false);
}

add_filter('the_content', 'monospace_ensure_oembed', 1);
function monospace_ensure_oembed($content) {
    return preg_replace('/^\s*<[^>]*>(http.*)<[^>]*>\s*$/im', '\1' . "\n", $content);
}

add_action('init', 'monospace_infinite_scroll');
function monospace_infinite_scroll() {

    if (is_admin())
        return false;

    $defaults = array(
        'action' => 'infinite_scroll',
        'type' => false,
        'type_id' => false,
        'page' => false
    );
    $args = wp_parse_args($_REQUEST, $defaults);

    if (!$args['action'] || !$args['page'] || !$args['type'])
        return false;

    $query_args = array('paged' => $args['page']);
    if ($args['type'] == 'category' && isset($args['type_id']))
        $query_args['cat'] = $args['type_id'];
    elseif ($args['type'] == 'tag' && isset($args['type_id']))
        $query_args['tag'] = $args['type_id'];

    $posts = new WP_Query($query_args);

	if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            get_template_part('post');
        }
    }

    exit();

}

function monospace_post_category($post_id) {
    $post_categories = wp_get_post_categories($post_id);
    if (count($post_categories) <= 0)
        return get_category(get_option('default_category'));
    return get_category($post_categories[0]);
}

add_filter('post_class', 'monospace_category_class');
function monospace_category_class($classes) {
    global $post;
    foreach((get_the_category($post->ID)) as $category)
        $classes[] = $category->category_nicename;
    return $classes;
}

add_filter('the_content', 'monospace_format_content');
function monospace_format_content($content) {

    preg_match_all('#<h[2-6][^>]*.*?</h[2-6]>#', $content, $matches);
    if (!$matches)
        return $content;

    for ($i = 0; $i < count($matches[0]); $i++) {
        $slug = sanitize_title($matches[0][$i]);
        $anchor = '<div class="clear"></div><a class="content-anchor" id="' . $slug . '" href="#' . $slug . '"></a>';
        $content = str_replace($matches[0][$i], $anchor . $matches[0][$i], $content);
    }

    return $content;

}

function monospace_scroll_params() {

    global $cat, $tag;

    $params = array();

    if (is_home()) {
        $params['type'] = 'home';

    } else if (is_category()) {
        $params['type'] = 'category';
        $params['type_id'] = $cat;

    } else if (is_tag()) {
        $params['type'] = 'tag';
        $params['type_id'] = $tag;

    } else {
        $params['type'] = false;
    }

    return $params;
}

add_action('monospace_header_loaded', 'monospace_run_header_js');
function monospace_run_header_js() {
    ?>
    <script type="text/javascript">
        format_header();
    </script>
    <?php
}

add_action('monospace_post_loaded', 'monospace_run_post_js');
function monospace_run_post_js($args) {
    $defaults = array(
        'post_id' => false
    );
    $args = wp_parse_args($args, $defaults);
    if (!$args['post_id'])
        return false;
    ?>
    <script type="text/javascript">
        format_post('.post-<?php echo $args['post_id']; ?>');
    </script>
    <?php
}

add_action('wp_enqueue_scripts', 'monospace_enqueue_scripts');
function monospace_enqueue_scripts() {
    if (is_singular()) wp_enqueue_script('comment-reply');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-jcarousel', get_stylesheet_directory_uri() . '/js/jquery.jcarousel.min.js', array('jquery'));
    wp_enqueue_script('jquery-hoverintent', get_stylesheet_directory_uri() . '/js/jquery.hoverintent.min.js', array('jquery'));
    wp_enqueue_script('monospace-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery', 'jquery-jcarousel', 'jquery-hoverintent'));
    wp_enqueue_script('addthis', 'http://s7.addthis.com/js/250/addthis_widget.js');
    $params = monospace_scroll_params();
    wp_localize_script('monospace-scripts', 'params', $params);
}

function monospace_strlimit($str, $limit = 100, $sufix = '[...]') {

    $c = 0;
    $limited = array();
    $str = preg_replace('#\s+#', ' ', $str);
    $str = explode(' ', strip_tags($str));

    for ($i = 0; $i < count($str); $i++) {
        if (($c += strlen($str[$i])) > $limit)
            break;
        $limited[] = $str[$i];
    }

    if ($c > $limit)
        $limited[] = ' ' . $sufix;

    return implode(' ', $limited);
}

add_action('wp_head', 'monospace_share_head');
function monospace_share_head() {
    global $post;
    if (!$post)
        return false;
    $properties = array();
    $post_img = false;

    $properties['og:site_name'] = get_bloginfo('name');
    $properties['og:title'] = apply_filters('the_title', $post->post_title);
    $properties['og:url'] = get_permalink($post->ID);

    if (preg_match('#<img.*src=["\'](.*)["\']#siU', apply_filters('the_content', $post->post_content), $matches))
        $post_img = $matches[1];
    $post_excerpt = (has_excerpt($post->ID)) ? $post->post_excerpt : $post->post_content;

    $properties['og:description'] = monospace_strlimit($post_excerpt);
    if ($post_img)
        $properties['og:image'] = $post_img;

    $regex = '#.*https?://.*\.youtu\.?be.*(?:watch)?.*v=([A-Za-z0-9]+).*#si';
    if (preg_match($regex, $post->post_content, $matches)) {
        $video_id = $matches[1];
        $properties['og:type'] = 'video';
        $properties['og:image'] = 'http://i4.ytimg.com/vi/' . $video_id . '/default.jpg';
        $properties['og:video'] = 'http://youtube.com/v/' . $video_id;
        $properties['og:video:width'] = 384;
        $properties['og:video:height'] = 264;
    }

    foreach ($properties as $k => $v) {
        ?>
        <meta property="<?php echo $k; ?>" content="<?php echo $v; ?>" />
        <?php
    }

}

add_action('wp_head', 'monospace_category_css');
function monospace_category_css() {
    $styles = array();
    $icons_base = get_stylesheet_directory_uri() . '/icon/';
    $categories = get_categories();
    $sizes = array(16, 48);
    foreach ($categories as $c) {

        $cat_class = 'category-icon-' . $c->term_id;
        if (!$icon = of_get_option($cat_class))
            $icon = MONOSPACE_DEFAULT_ICON;

        foreach ($sizes as $s) {
            $styles[] = '.' . $cat_class . '-' . $s . ' { background-image:url("' . $icons_base . $icon . $s . '.png"); }';
            $styles[] = '.' . $cat_class . '-' . $s . ':hover { background-image:url("' . $icons_base . $icon . $s . '-hover.png"); }';
        }

    }
    ?>
    <style type="text/css"><?php echo implode("\r", $styles); ?></style>
    <?php
}

function monospace_get_icons() {

    $icons = array();
    $path = get_stylesheet_directory() . '/icon/';
    $dir = opendir($path);

    while ($file = readdir($dir)) {

        if (strpos($file, '16.png') === false)
            continue;

        $name = preg_replace('#&.*#', '', $file);
        $name = preg_replace('#[^0-9A-Za-z]#', ' ', $name);
        $name = ucwords(trim(preg_replace('#png$#', ' ', $name)));
        $icons[] = array(
            'name' => $name,
            'file' => $file,
        );

    }

    usort($icons, 'monospace_get_icons_sort');
    return $icons;
}

function monospace_get_icons_sort($a, $b) {
    if ($a['file'] > $b['file'])
        return 1;
    else
        return -1;
}

function monospace_meta () {
    global $post;
    $categories = get_the_category();
    $tags = get_the_tags();
    ?>
    <div class="meta">

        <a class="icon16 star-icon12 share-button" rel="<?php echo $post->ID; ?>" href="#"><?php _e('Share', 'monospace2'); ?></a>
        <a class="icon16 comment-icon12" href="<?php comments_link(); ?>"><?php comments_number('0', '1', '%'); ?></a>

        <?php if ($categories) : ?>
            <?php foreach ($categories as $c) : ?>
                <a class="categories icon16 category-icon-<?php echo $c->term_id; ?>-16"
                    href="<?php echo get_category_link($c->term_id); ?>"
                    title="<?php _e('Link to the category', 'monospace2'); ?> <?php echo $c->name; ?>">
                    <?php echo $c->name; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($tags) : ?><span class="tags icon12 tags-icon12"><?php the_tags('', ''); ?></span><?php endif; ?>

        <?php edit_post_link(); ?>
        <a class="icon16 link-internal-icon12" href="<?php echo get_permalink($post->ID); ?>" title="<?php _e('Permalink to', 'monospace2'); ?> <?php the_title(); ?>"></a>

        <?php $addthis = 'addthis:url="' . get_permalink($post->ID) . '" addthis:title="' . get_the_title($post->ID) . '"'; ?>
        <div class="share">
            <div class="addthis_toolbox addthis_default_style">
                <a <?php echo $addthis; ?> class="addthis_button_preferred_1"></a>
                <a <?php echo $addthis; ?> class="addthis_button_preferred_2"></a>
                <a <?php echo $addthis; ?> class="addthis_button_preferred_3"></a>
                <a <?php echo $addthis; ?> class="addthis_button_preferred_4"></a>
                <a <?php echo $addthis; ?> class="addthis_button_compact"></a>
            </div>
            <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-5021cd7a49db1f38"></script>
        </div>

    </div>
    <?php
}

function monospace_title ($mode = false) {
    global $page, $paged;

    $title = wp_title('', false);

    if (is_category())
        echo __('Category', 'monospace2').' | ';
    elseif (is_tag())
        echo __('Tag', 'monospace2').' | ';
    elseif (is_archive())
        echo __('Archives', 'monospace2').' | ';
    elseif (is_search())
        $title = preg_replace ('/^\ \ \|\ \ /', '', $title);

    echo $title;

    if ($mode == 'head') {
        if (!is_home())
            echo ' | ';
        bloginfo( 'name' );
        if ($site_description = get_bloginfo( 'description', 'display' ))
            echo " | $site_description";
    }

    if ( $paged >= 2 || $page >= 2 )
        echo ' | ' . sprintf( __( 'Page %s', 'monospace2' ), max( $paged, $page ) );
}

function monospace_navigation() {

    global $post;

    if (!is_single())
        return false;

    if ($prev = get_previous_post())
        $prev_post_url = get_permalink($prev->ID);
    else
        $prev_post_url = false;

    if ($next = get_next_post())
        $next_post_url = get_permalink($next->ID);
    else
        $next_post_url = false;

    ?>

    <?php if ($prev) : ?>
        <div class="nav-link prev-link">
            <a class="nav-link-icon"
                title="<?php _e('Permalink to', 'monospace2'); ?> <?php echo $prev->post_title; ?>"
                href="<?php echo $prev_post_url; ?>"></a>
            <span><?php echo $prev->post_title; ?></span>
        </div>
    <?php endif; ?>

    <?php if ($next) : ?>
        <div class="nav-link next-link">
            <a class="nav-link-icon"
                title="<?php _e('Permalink to', 'monospace2'); ?> <?php echo $next->post_title; ?>"
                href="<?php echo $next_post_url; ?>"></a>
            <span><?php echo $next->post_title; ?></span>
        </div>
    <?php endif; ?>

    <?php
}

?>
