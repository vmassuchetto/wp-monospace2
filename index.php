<?php get_header (); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php get_template_part('post'); ?>

	<?php endwhile; ?>

	<?php else : ?>

        <?php _e('No posts found', 'monospace2'); ?>

	<?php endif; ?>

<?php get_footer (); ?>
