<div class="menu-categories">
    <?php $icons_base = get_stylesheet_directory_uri() . '/icon/'; ?>
    <ul>
        <?php foreach (get_categories() as $category) : ?>

            <?php
                $class = 'category-icon-' . $category->term_id;
                $title = sprintf(__('Link to the category $1', 'monospace2'), $category->name);
            ?>

            <li class="menu-item menu-item-category-<?php echo $category->slug; ?>">
                <?php if ($icon = of_get_option($class)) : ?>
                    <a class="menu-item-icon icon48 <?php echo $class; ?>"
                        href="<?php echo get_category_link($category->term_id); ?>"
                        title="<?php echo $title; ?>"></a>
                <?php endif; ?>
                <a class="menu-item-text icon48"
                    href="<?php echo get_category_link($category->term_id); ?>"
                    title="<?php echo $title; ?>"><?php echo $category->name; ?></a>
            </li>

        <?php endforeach; ?>
    </ul>
</div>

<div class="menu-tags">
    <?php wp_tag_cloud(array('smallest' => 10, 'largest' => 32, 'unit' => 'px', 'format' => 'list')); ?>
</div>
