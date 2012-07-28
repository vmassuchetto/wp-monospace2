<h3 id="comments" class="icon32 comments-icon32"><?php comments_number(__('Nenhum Comentário', 'monospace2'), __('1 Comentário', 'monospace2'), __('% Comentários', 'monospace2')); ?></h3>
<ol class="commentlist"><?php wp_list_comments(array('avatar_size' => 48)); ?></ol>
<?php comment_form(); ?>
