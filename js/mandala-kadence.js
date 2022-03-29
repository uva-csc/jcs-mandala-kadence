(function($) {
    subsite_title();
    function subsite_title() {
        if ($('#subsite-title').length > 0) {
            const subtitle = $('#subsite-title').html();
            $('.site-branding .site-title-wrap .site-title').fadeOut(function() {
                $(this).html(subtitle);
                $(this).fadeIn();
            });
        }
    }
})(jQuery);