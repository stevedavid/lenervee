jQuery(function($) {

    $('[rel=popover].courrier-link').popover({
        trigger: 'hover',
        placement: 'top',
    });
    $('[rel=popover].courrier-link').on('mouseover', function() {
        var image = $(this).data('image');
        $('.popover').find('h3').css({
            'background-image': 'url("'+image+'")',
        });
    });

    $('a.fb-sharer, a.twitter-sharer, a.gp-sharer').on('click', function(e) {
        var href = $(this).attr('href');
        e.preventDefault();
        window.open(href,"nom_popup","menubar=no, status=no, scrollbars=no, menubar=no, width=483, height=253");

    });


    tw_3 = jQuery('.testimonials-widget-testimonials3').bxSlider({
        adaptiveHeight: false,
        auto: true,

        autoHover: true,
        controls: false,
        mode: 'horizontal',
        pager: false,
        pause: 10000,
        video: false,
        slideMargin: 2,
        slideWidth: 0
    });
});