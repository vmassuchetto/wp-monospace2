<?php

class WidgetCategoryCarousel extends WP_Widget {

    function WidgetCategoryCarousel () {
        $widget_ops = array('description' => __('Category carousel with icons', 'monospace2'));
        $this->WP_Widget('monospace2_category_carousel', __('Monospace2: Category Carousel', 'monospace2'), $widget_ops);
    }

    function widget ($args, $inst) {
        $icons_base = get_stylesheet_directory_uri() . '/icon/';
        ?>
        <div class="menu-categories">
            <ul>
                <?php foreach (get_categories() as $category) : ?>
                    <?php
                        $class = 'category-icon-' . $category->term_id . '-48';
                        $title = __('Link to the category', 'monospace2') . ' ' . $category->name;
                    ?>
                    <li class="menu-item menu-item-category-<?php echo $category->slug; ?>">
                        <a class="menu-item-icon icon48 <?php echo $class; ?>"
                            href="<?php echo get_category_link($category->term_id); ?>"
                            title="<?php echo $title; ?>"></a>
                        <a class="menu-item-text icon48"
                            href="<?php echo get_category_link($category->term_id); ?>"
                            title="<?php echo $title; ?>"><?php echo $category->name; ?></a>
                    </li>

                <?php endforeach; ?>
            </ul>
        </div>
        <script type="text/javascript">
            jQuery('.menu-categories').jcarousel({
                'animation': 'slow',
                'wrap': 'circular',
                'itemFallbackDimension': 110
            });
        </script>
        <?php
    }

}

class WidgetTagCarousel extends WP_Widget {

    function WidgetTagCarousel () {
        $widget_ops = array('description' => __('Tag carousel', 'monospace2'));
        $this->WP_Widget('monospace2_tag_carousel', __('Monospace2: Tag Carousel', 'monospace2'), $widget_ops);
    }

    function widget ($args, $inst) {
        $icons_base = get_stylesheet_directory_uri() . '/icon/';
        ?>
        <div class="menu-tags">
            <?php wp_tag_cloud(array('smallest' => 12, 'largest' => 28, 'unit' => 'px', 'format' => 'list')); ?>
        </div>
        <script type="text/javascript">
            jQuery('.menu-tags').jcarousel({
                'animation': 'slow',
                'wrap': 'circular',
                'scroll': 5,
                'setupCallback': function(carousel) {
                    jQuery('.menu-tags a').each(function(){
                        item = jQuery(this);
                        w = item.css('font-size').replace('px', '')
                            * item.html().length * 0.6;
                        item.parent().css('width', w + 'px');
                    });
                }
            });
        </script>
        <?php
    }

}

add_action('widgets_init', 'monospace_widgets_init');
function monospace_widgets_init() {
    register_widget('WidgetCategoryCarousel');
    register_widget('WidgetTagCarousel');
}

?>
