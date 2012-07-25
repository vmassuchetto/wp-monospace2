function format_post(post) {

    post = jQuery(post);

    var pattern = '/' + document.domain + '/';
    post.find('.entry a').each(function(){
        if (jQuery(this).find('img').length > 0
            || jQuery(this).hasClass('content-anchor'))
            return true;
        jQuery(this).addClass('icon12');
        href = jQuery(this).attr('href');
        if (href !== undefined && href.match(pattern))
            jQuery(this).addClass('link-internal-icon12');
        else
            jQuery(this).addClass('link-external-icon12');
    });

    post.find('.entry img').each(function(){
        p = jQuery(this).parents('p');
        if (p.length <= 0)
            return false;
        p.css('text-align', 'center');
    });

    post.find('.entry iframe').each(function(){
        p = jQuery(this).parents('p');
        if (p.length <= 0) {
            i = jQuery('<div>').append(jQuery(this).clone()).html();
            p = jQuery('<p>');
            p.append(i);
            jQuery(this).replaceWith(p);
        }
        p.css('text-align', 'center');
    });

}

jQuery(document).ready(function() {

    jQuery('.nav-link').hover(function(){
        jQuery(this).find('span').stop().fadeIn(200);
    }, function() {
        jQuery(this).find('span').stop().fadeOut(200);
    });

    jQuery('.comment-author img').hover(function(){
        jQuery(this).next('.fn').stop().fadeIn(200);
    });

    jQuery('.entry h3, .entry h4, .entry h5, .entry h6').hover(function(){
        jQuery(this).prev().fadeIn(200);
    });

    var menu_height;
    menu_height = 300;

    jQuery('.menu-icon24').toggle(function(e){
        e.preventDefault();
        jQuery('.menu-wrap').stop().animate({ height: '+=' + menu_height + 'px' }, 200);
        jQuery(this).addClass('menu-icon24-toggled');
    }, function(e){
        e.preventDefault();
        jQuery('.menu-wrap').animate({ height: '65px' }, 200);
        jQuery(this).removeClass('menu-icon24-toggled');
    });

    jQuery('.menu-categories').jcarousel({
        'animation': 'slow',
        'wrap': 'circular',
    });
    jQuery('.menu-tags').jcarousel({
        'animation': 'slow',
        'wrap': 'circular',
        'scroll': 6,
        'setupCallback': function(carousel) {
            jQuery('.menu-tags a').each(function(){
                item = jQuery(this);
                w = item.css('font-size').replace('px', '')
                    * item.html().length * 0.7;
                item.parent().css('width', w + 'px');
            });
        }
    });


    if (params.type) {

        var page = 1;
        params.scrolling = false;

        jQuery(window).scroll(function () {

            if (jQuery(window).scrollTop() ==
                jQuery(document).height() - jQuery(window).height()) {

                    if (params.scrolling)
                        return false;
                    params.scrolling = true;

                    page++;
                    if (jQuery('.infinite-scroll-wrap').height() < 60)
                        jQuery('.infinite-scroll-wrap').animate({ height: '+=60px' }, 200);
                    jQuery.ajax({
                        url: location.href,
                        type: 'GET',
                        data: {
                            action: 'infinite_scroll',
                            type: params.type,
                            type_id: params.type_id,
                            page: page
                        }, success: function(data) {
                            jQuery('#container').append(data);
                            jQuery('.infinite-scroll-wrap').animate({ height: '0px' }, 200);
                            params.scrolling = false;
                        }
                    });

            }

        });

    }

});
