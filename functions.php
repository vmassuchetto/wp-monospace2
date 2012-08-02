<?php

define ('MONOSPACE_CREDITS_URL', 'http://vinicius.soylocoporti.org.br/monospace-wordpress-theme/');
define ('MONOSPACE_DEFAULT_ICON', 'box_icon&');

include (get_stylesheet_directory() . '/options.php');
load_theme_textdomain ('monospace2', get_template_directory().'/lang');

register_sidebar(array(
	'name'          => __('Left Menu', 'monospace'),
	'id'            => 'menu-left',
	'description'   => '',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '',
	'after_title'   => ''
));
register_sidebar(array(
	'name'          => __('Middle Menu', 'monospace2'),
	'id'            => 'menu-middle',
	'description'   => '',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '',
	'after_title'   => ''
));
register_sidebar(array(
	'name'          => __('Right Menu', 'monospace2'),
	'id'            => 'menu-right',
	'description'   => '',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '',
	'after_title'   => ''
));

remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );
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
	foreach((get_the_category($post->ID)) as $category) {
	        $classes[] = $category->category_nicename;
	        return $classes;
	}
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

add_action('wp_enqueue_scripts', 'monospace_enqueue_scripts');
function monospace_enqueue_scripts() {
    if (is_singular()) wp_enqueue_script('comment-reply');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-jcarousel', get_stylesheet_directory_uri() . '/js/jquery.jcarousel.min.js', array('jquery'));
    wp_enqueue_script('monospace-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery', 'jquery-jcarousel'));
    $params = monospace_scroll_params();
    wp_localize_script('monospace-scripts', 'params', $params);
}

add_action('wp_head', 'monospace_head');
function monospace_head() {
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
        $name = preg_replace('#icon#', ' ', $name);
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

        <a class="icon16 comment-icon12" href="<?php comments_link(); ?>"><?php comments_number('0', '1', '%'); ?></a>

        <?php if ($categories) : ?>
            <?php foreach ($categories as $c) : ?>
                <a class="categories icon16 category-icon-<?php echo $c->term_id; ?>-16" href="<?php echo get_category_link($c->term_id); ?>"><?php echo $c->name; ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($tags) : ?><span class="tags icon12 tags-icon12"><?php the_tags('', ''); ?></span><?php endif; ?>

        <?php edit_post_link(); ?>

    </div>
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
    <?
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

function monospace_share () {
    global $post;
    $url = urlencode(get_permalink($post->ID));
    $title = urlencode(get_the_title());
    ?>
    <div class="meta">
        <span class="share">
            <a class="icon16 facebook-icon16" href="<?php echo esc_url('http://facebook.com/sharer.php?t=' . $title . '&amp;u=' . $url); ?>" target="_blank">Facebook</a>
            <a class="icon16 twitter-icon16" href="<?php echo esc_url('http://twitter.com/home?status=' . $title . '%20' . $url); ?>" target="_blank">Twitter</a>
            <a class="icon16 google-icon16" href="<?php echo esc_url('http://google.com/reader/link?title=' . $title . '&amp;url=' . $url);?>" target="_blank">Google Reader</a>
            <a class="icon16 share-icon16" href="<?php echo esc_url('http://ping.fm/ref/?conduit&method=status&title=' . $title . '&link=' . $url); ?>" target="_blank">Ping.fm</a>
        </span>
    </div>
    <?php
}

function monospace_related () {
    global $wpdb, $post,$table_prefix;
    ?>

    <?php

    if(!$post->ID){return;}

    $now = current_time('mysql', 1);

    $tags = wp_get_post_tags($post->ID);
    $tagcount = count($tags);
    $taglist = '';
    if ($tagcount)
        $taglist = "'" . $tags[0]->term_id. "'";

    if ($tagcount > 1) {
        for ($i = 1; $i < $tagcount; $i++) {
            $taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
        }
    }

    if ($taglist)
        $taglist = "AND (t_t.term_id IN ($taglist))";

    $q = "
        SELECT p.ID, p.post_title, p.post_content,p.post_excerpt,
            p.post_date, p.comment_count, count(t_r.object_id) as cnt
        FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r,
            $wpdb->posts p
        WHERE 1
            AND t_t.taxonomy ='post_tag'
            AND t_t.term_taxonomy_id = t_r.term_taxonomy_id
            AND t_r.object_id  = p.ID
            AND p.ID != $post->ID
            AND p.post_status = 'publish'
            AND p.post_date_gmt < '$now'
            $taglist
        GROUP BY t_r.object_id
        ORDER BY
            cnt DESC,
            p.post_date_gmt DESC
        LIMIT 5;
    ";

    $related_posts = $wpdb->get_results($q);

    if (!$related_posts) {
        $q = "
            SELECT ID, post_title, post_content, post_excerpt,
                post_date,comment_count
            FROM $wpdb->posts
            WHERE 1
                AND post_status = 'publish'
                AND post_type = 'post'
                AND ID != $post->ID
            ORDER BY RAND()
            LIMIT 5
        ";
        $related_posts = $wpdb->get_results($q);
    }

    $i = 0;
    $output = '';
    foreach ($related_posts as $related_post ){
        $datestr = strtotime($related_post->post_date);
        $dm = date('d/m', $datestr);
        $y = substr(date('Y', $datestr),2,2);
        $output .= '
            <li>
                <a href="'.get_permalink($related_post->ID).'" title="'.wptexturize($related_post->post_title).'">'.wptexturize($related_post->post_title).'</a>
            </li>
        ';

    }

    if (!$output)
        return false;
    else
        echo '<div class="related"><h3>'.__('Related Posts', 'monospace2').'</h3><ul>'.$output.'</ul></div>';

}

?>
