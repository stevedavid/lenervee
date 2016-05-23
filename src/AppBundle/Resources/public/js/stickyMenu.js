jQuery(function($) {

    console.log($(window).scrollTop());
    var lastScrollTop = $(this).scrollTop() ? $(this).scrollTop() - 1 : 0;

    var width = $('#sidebar').width();
    stickyMenu = function (first) {

        var $journalContent = $('.journal-content')
            , $sidebarInner = $('.sidebar-inner');
        //console.log($sidebarInner.outerHeight());
        if ($journalContent.outerHeight() > $sidebarInner.outerHeight() && $(window).width() > 768) {
            var st = $(this).scrollTop()
                , windowHeight = parseInt($(window).height())
                , bottom = st + windowHeight // position of bottom of windows
                , stickyLeft = $journalContent.offset().left + parseInt($journalContent.width())
                , newOffset = $sidebarInner.height() - windowHeight
                , headerHeight = $journalContent.offset().top
                , footerTop = $('#footer').offset().top // offset top du footer
                , innerSidebarTop = first ? headerHeight : $sidebarInner.offset().top
                , offset = first ? headerHeight + innerSidebarTop : window.whenToStick; // bottom des #sidebar > aside

            window.positionTop = innerSidebarTop > 0 ? innerSidebarTop : headerHeight;

            //if(first)
            //{
            //    offset = headerHeight;
            //}
            //console.log('innerSidebarTop ' + innerSidebarTop);

            // pas passé vers le haut et plus que le header
            if (window.positionTop >= st && st < lastScrollTop && st > headerHeight) {
                $('#sidebar').addClass('fixed').width(width).css({
                    'top': '0px',
                    'left': stickyLeft,
                });
                //console.log('>>> pas passé vers le haut et plus que le header');

                // passé vers le haut moins que le header
            } else if (offset - $('#footer').height() - 70 <= bottom && st < lastScrollTop) {
                //console.log('positionTop ' + window.positionTop);
                $('#sidebar').removeClass('fixed').width('33.341%').css({
                    'top': innerSidebarTop > 0 ? window.positionTop - headerHeight : '0px',
                    //'top': '0px',
                    'left': 'auto',
                });
                //window.positionTop = innerSidebarTop;
                //console.log('>>> passé vers le haut moins que le header');
                // passé et vers le bas
            } else if (offset <= bottom && st > lastScrollTop && bottom < footerTop) {
                $('#sidebar').addClass('fixed').width(width).css({
                    //'top': '-' + newOffset - 70 + 'px',
                    'top': innerSidebarTop > 0 ? '-'+newOffset - 70+'px' : '0px',
                    'left': stickyLeft,
                });
                //console.log('window.positionTop ' + window.positionTop);
                //console.log('>>> passé et vers le bas');
                //window.positionTop = $('.sidebar-inner').offset().top;
                //    // pas passé vers le haut et moins que le header
                //} else if (window.positionTop >= bottom - windowHeight && st < lastScrollTop && st <  headerHeight) {
                //    $('#sidebar').addClass('fixed').width(width).css({
                //        'top': '0px',
                //        'left': stickyLeft,
                //    });
                //    console.log('>>> pas passé vers le haut et moins que le header');
                // vers le haut et plus haut que le header
                //} else if (st < headerHeight && st < lastScrollTop) {
                //    $('#sidebar').removeClass('fixed').width('33.341%').css({
                //        'top': 'auto',
                //        'left': 'auto',
                //    });
                //    console.log('vers le haut et plus haut que le header');
                // vers le bas et plus bas que le footer
            } else if (st > lastScrollTop && bottom > footerTop) {
                $('#sidebar').removeClass('fixed').width('33.341%').css({
                    'top': window.positionTop - headerHeight,
                    'left': 'auto',
                });
                //console.log('>>> vers le bas et plus bas que le footer');
                //window.positionTop = $('.sidebar-inner').offset().top;
                // first
            //} else if (first && typeof offset == 'undefined' && bottom > footerTop) {
            } else if (first) {
                $('#sidebar').removeClass('fixed').width('33.341%').css({
                    'top': 'auto',
                    'left': 'auto',
                });
                //window.positionTop = $('.sidebar-inner').offset().top;
                //console.log('>>> first');
                // le reste
            } else {
                $('#sidebar').removeClass('fixed').width('33.341%').css({
                    'top': 'auto',
                    'left': 'auto',
                });
                //console.log('>>> le reste');
            }

            lastScrollTop = st;
        }

    };
    $(window).on('scroll', function () {
        stickyMenu(false);
    });
    stickyMenu(true);
});

$(window).load(function() {
    var totalHeight = $('.sidebar-inner').offset().top;

    //window.positionTop = totalHeight;
    $('.sidebar-inner').find('aside').each(function() {
        totalHeight += parseInt($(this).outerHeight());
    });
    console.log(totalHeight);
    window.whenToStick = totalHeight;
});