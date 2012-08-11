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

<div id="wrap" style="width:<?php echo $content_width; ?>px;">

    <?php monospace_navigation(); ?>

    <div id="header">
        <div class="menu-wrap">
            <div class="menu-inner">
            </div>
        </div>
        <span class="menu-icon24-wrap"><a class="icon16 menu-icon24" href="#"></a></span>
    </div>

    <div id="container">
