<?php

if (!function_exists('optionsframework_init')) {
    define('OPTIONS_FRAMEWORK_DIRECTORY', get_stylesheet_directory_uri() . '/inc/');
    require_once dirname(__FILE__) . '/inc/options-framework.php';
    add_action ('admin_enqueue_scripts', 'optionsframework_custom_admin_scripts');
}

function optionsframework_custom_admin_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ddslick', get_stylesheet_directory_uri() . '/js/jquery.ddslick.min.js', array('jquery'));
    wp_enqueue_style('monospace-admin', get_stylesheet_directory_uri() . '/style-admin.css');
}

function optionsframework_option_name() {
    $themename = wp_get_theme();
    $themename = $themename['Name'];
    $themename = preg_replace("/\W/", "", strtolower($themename));

    $optionsframework_settings = get_option('optionsframework');
    $optionsframework_settings['id'] = $themename;
    update_option('optionsframework', $optionsframework_settings);
}

function optionsframework_options() {

    global $wpdb;

    $categories = get_categories(array('hide_empty' => false));
    $options = array();
    $icons_base = get_stylesheet_directory_uri() . '/icon/';

    $categories_multicheck = array();
    foreach ($categories as $c)
        $categories_multicheck[$c->term_id] = '&nbsp;' . $c->name;

    $options[] = array(
        'name' => __('Style & Appearance', 'monospace2'),
        'type' => 'heading');

    $options[] = array(
        'name' => __('Disable Titles', 'monospace2'),
        'desc' => __('You can hide titles for categories that may not need it. The intention of this option is to give a timeline appearance to some kind of posts like tweets, pictures and videos. The side icon of these categories will still be visible.', 'monospace2'),
        'id' => 'show_category_title',
        'std' => array(),
        'type' => 'multicheck',
        'options' => $categories_multicheck
    );

    $options[] = array(
        'name' => __('Category Icons', 'monospace2'),
        'type' => 'heading');

    foreach ($categories as $c) {
        $class = 'category-icon-' . $c->term_id;
        $options[] = array(
            'name'    => '&nbsp;' . $c->name,
            'id'      => $class,
            'type'    => 'text',
        );
    }

    $options[] = array(
        'name' => __('Features', 'monospace2'),
        'type' => 'heading');

	$options[] = array(
        'name' => __('Enable Posts Views Count', 'monospace2'),
        'desc' => '&nbsp;' . __('Will enable a views counter for every post
            and display it in the meta section.', 'monospace2'),
        'id' => 'views_count',
        'std' => '0',
        'type' => 'checkbox');

    return $options;
}

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');
function optionsframework_custom_scripts() { ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {

        $('#example_showhidden').click(function() {
            $('#section-example_text_hidden').fadeToggle(400);
        });

        if ($('#example_showhidden:checked').val() !== undefined) {
            $('#section-example_text_hidden').show();
        }

        data = [];
        indexes = [];

        <?php $n = 0; ?>
        <?php $icon_base = get_stylesheet_directory_uri() . '/icon/'; ?>
        <?php foreach (monospace_get_icons() as $i) : ?>
            data.push({
                text: '<?php echo $i['name']; ?>',
                value: '<?php echo $i['file']; ?>',
                imageSrc: '<?php echo $icon_base . $i['file']; ?>',
                selected: false,
            });
            indexes.push('<?php echo preg_replace('#&.*#', '&', $i['file']); ?>');
        <?php endforeach; ?>

        jQuery('.of-input').each(function(){

            var field = jQuery(this);

            if (!field.attr('id').match(/^category-icon.*/))
                return true;

            field.hide();
            field.after('<div></div>');
            ddslickdiv = field.next('div');
            ddslickdiv.ddslick({
                data: data,
                width: 300,
                imagePosition: "left",
                selectText: "<?php _e('Select icon', 'monospace2'); ?>",
                onSelected: function (data) {
                    field.val(data.selectedData.value.replace('16.png', ''));
                }
            });

            if (field.val())
                field.parent()
                    .next('.explain')
                    .html('<img src="<?php echo $icon_base; ?>' + field.val() + '16.png" />');

        });

    });
    </script>
<?php
}

?>
