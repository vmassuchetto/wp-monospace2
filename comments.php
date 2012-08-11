<h3 id="comments" class="icon32 comments-icon32"><?php comments_number(__('No Comment', 'monospace2'), __('1 Comment', 'monospace2'), __('% Comments', 'monospace2')); ?></h3>
<?php comment_form(); ?>
<ol class="commentlist"><?php wp_list_comments(array('avatar_size' => 48, 'reverse_top_level' => true, 'reverse_children' => false)); ?></ol>
<?php paginate_comments_links(); ?>
