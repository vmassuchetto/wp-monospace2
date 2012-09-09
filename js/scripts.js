function views_count(post_id) {
    jQuery.ajax({
        url: location.href,
        type: 'POST',
        data: {
            action: 'monospace_views_count',
            post_id: post_id
        }
    });
}

function center_element(e) {

    p = jQuery(e).parents('p');
    if (p.length <= 0)
        return false;

    if (p.prev('p').length > 0 && p.next('p').length > 0)
        p.css('text-align', 'center');

}

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

    post.find('img').each(function(){
        center_element(this);
    });

    post.find('.share-button').click(function(e){
        e.preventDefault();
        post_id = jQuery(this).attr('rel');
        jQuery('#post-' + post_id).find('.share').slideDown(200);
    });

}

function format_header() {

    jQuery('.menu-header-items li a').hoverIntent({
        timeout:150,
        over: function(){
            jQuery(this).parent('li').find('ul:first')
                .stop()
                .slideDown(100)
                .css('overflow', 'visible');
        },
        out: function() { }
    });

    jQuery('.menu-header-items ul li ul').hoverIntent({
        timeout:300,
        over: function() { },
        out: function(){
            jQuery(this).slideUp(100);
        }
    });

    jQuery('.menu-icon24').toggle(function(e){
        e.preventDefault();
        menu_height = jQuery('.menu-inner').height() + 20;
        jQuery('.menu-wrap')
            .stop()
            .animate({ height: '+=' + menu_height + 'px' }, 200);
        jQuery(this).addClass('menu-icon24-toggled');
    }, function(e){
        e.preventDefault();
        jQuery('.menu-wrap').animate({ height: '65px' }, 200);
        jQuery(this).removeClass('menu-icon24-toggled');
    });
}

jQuery(document).ready(function() {

    jQuery('.nav-link').hover(function(){
        jQuery(this).find('span').stop().fadeIn(200);
    }, function() {
        jQuery(this).find('span').stop().fadeOut(200);
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
