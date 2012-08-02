<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="post-logo">
        <?php
            $c = monospace_post_category(get_the_ID());
            $title_class = '';
            if ($show_title = of_get_option('show_category_title'))
                if ($show_title[$c->term_id])
                    $title_class = 'category-hidden category-hidden-' . $c->term_id;
        ?>
        <a title="<?php printf(__('Link to the category $1', 'monospace2'), $c->name); ?>"
            href="<?php echo get_category_link($c->term_id); ?>"
            class="category-icon-<?php echo $c->term_id; ?>-48">
        </a>
    </div>

    <div class="post-content">
        <?php $title = (get_the_title()) ? get_the_title() : __('Untitled', 'monospace'); ?>
        <h2<?php echo ($title_class) ? ' class="' . $title_class . '"' : ''; ?>><a href="<?php the_permalink(); ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></h2>

        <div class="entry">
            <?php the_content(); ?>
            <?php wp_link_pages(); ?>
        </div>

        <?php monospace_meta(); ?>
        <?php comments_template(); ?>

    </div>

</div>

<?php do_action('monospace_post_loaded', array('post_id' => get_the_ID())); ?>
