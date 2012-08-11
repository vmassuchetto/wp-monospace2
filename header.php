<?php global $content_width; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" />
	<title><?php monospace_title('head'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php _e('Posts Feed', 'monospace2'); ?> &raquo; <?php bloginfo('name'); ?>" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php _e('Posts Feed', 'monospace2'); ?> &raquo; <?php bloginfo('name'); ?>" href="<?php bloginfo('atom_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php _e('Comments Feed', 'monospace2'); ?> &raquo; <?php bloginfo('name'); ?>" href="<?php bloginfo('atom_url'); ?>" />
	<?php wp_head(); ?>
</head>

<body <?php if (function_exists('body_class')) body_class(); ?>>

<div id="wrap">

    <?php monospace_navigation(); ?>

    <div id="header">

        <div class="menu-header">
            <h1><a href="<?php echo home_url(); ?>"><?php bloginfo('title'); ?></a></h1>
            <span class="description"><?php bloginfo('description'); ?></span>
        </div>

        <?php
            $items_wrap = '<div class="menu-header-items"><ul>%3$s'
                . '<li class="rss_item"><a class="icon16" href="' . get_bloginfo('rss2_url') . '">RSS</a></li>'
                . '</ul></div>';
            wp_nav_menu(array(
                'theme_location' => 'header',
                'items_wrap' => $items_wrap,
                'fallback_cb' => false
            ));
        ?>

        <div class="menu-wrap">
            <div class="menu-inner">
                <?php dynamic_sidebar('expandable-menu'); ?>
                <p class="credits"><a href="<?php echo MONOSPACE_CREDITS_URL; ?>">Monospace2 WordPress Theme</a> by Vinicius Massuchetto</a>
            </div>
        </div>

        <span class="menu-icon24-wrap"><a class="icon16 menu-icon24" href="#"></a></span>

    </div>
    <?php do_action('monospace_header_loaded'); ?>

    <div id="container">
