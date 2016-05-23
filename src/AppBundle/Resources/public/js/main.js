/*global jQuery:false, $:false, addthis:false, addthis_config:false, Pace:false, Back:false, TweenLite:false, TweenMax:false, addthis_share:false,google:false*/
window.jQuery = window.$ = jQuery;

var $body = $('body');

function calculateHP() {
    return parseInt( $('#header.sticky #navigation').attr('data-top'), 10 ) + 100;
}

function calculateHRP() {
    return parseInt( $('#header.sticky #navigation').attr('data-top'), 10 );
}

if( $('#header.sticky').length ) {
    var headerPosition = calculateHP();
    var headerRealPosition = calculateHRP();
}

if( $('#sidebar.sticky').length ) {
    var sidebarRealPosition = $('#sidebar').length ? $('#sidebar').outerHeight() + $('#sidebar').offset().top : 0;
}

var removeNot;
var sidebarOffsetTop = $('#sidebar').length ? $('#sidebar').offset().top : 0;
var isMobile = window.bw_options.ismobile;

var App = {
    
    start: function() {
        
        if( ! isMobile ) {
            App.setContainers();
        }
        
        if( $('#header.sticky').length && ! isMobile ) {
            App.stickyHeader();
        }
        //
        //if( $('#sidebar.sticky').length && ! isMobile ) {
        //    App.stickySidebar();
        //}
        
        App.onSmartResize();
        
        App.onLoad();
        
        App.onReady();
        
        App.slider();
        
        App.bind();
        
        App.magnificPopup();
        
        App.isotope();
        
        App.pace();
        
        App.mapping();
        
        App.justifyGallery();
        
        App.addThis();
        
        App.imgLiquify();
        
        App.bwTa.start();
        
    },
    
    bwTa: {
        
        start: function() {
            
            this.bind();
            
        },
        
        bind: function() {
            
            var self = this;
            
            $('.post-heading-nav:not(.disabled)').on('mouseenter', function() {
                self.move( true, $(this).hasClass('nav-next') );
            }).on('mouseleave', function() {
                self.move( false, $(this).hasClass('nav-next') );
            });
            
        },
        
        move: function( show, isNext ) {
            
            $toMove = $('.bw-ta.ta-' + ( isNext ? 'next' : 'prev' ) );
            
            if( show ) {
                $toMove.addClass('show');
            }else{
                $toMove.removeClass('show');
            }
            
            this.moveCurrent( show, isNext );
            
        },
        
        moveCurrent: function( show, isNext ) {
            
            if( show ) {
                $('.bw-current').addClass('hidden-' + ( isNext ? 'bottom' : 'top' ) );
            }else{
                $('.bw-current').removeClass('hidden-bottom hidden-top');
            }
            
        }
        
    },
    
    imgLiquify: function() {
        
        $(".block-image-liquify").imgLiquid({
            fill: true
        });
        
    },
    
    addThis: function() {
        $('.addthis-dynamic a').on('click', function() {
            var theurl = $(this).closest('.addthis-dynamic').attr('data-url');
            
            addthis.update('share', 'url', theurl); 
            addthis.ready(); // This will re-render the box.
            $('meta[property="og:url"]').attr('content', theurl);
            
        });
    },
    
    justifyGallery: function() {
        
        var $justified = $(".bw-justified-gallery");
        
        $justified.each(function() {
            var self = $(this);
            self.justifiedGallery({
                rowHeight: self.attr('data-row-height') ? parseInt(self.attr('data-row-height')) : 400,
                fixedHeight: false,
                lastRow: 'justify',
                margins: 10,
                randomize: false,
                captionSettings : { animationDuration : 500, visibleOpacity : 0, nonVisibleOpacity : 0 },
            });
        });
        
    },
    
    mapping: function() {
        
        if( $('.rockme-map').length && typeof google !== 'undefined' ) {
            $('.rockme-map').rockmeMap();
        }
        
        if( $('.fullwidthmapwrapper').length && typeof google !== 'undefined' ) {
            $('.fullwidthmapwrapper').rockmeMap();
        }
        
    },
    
    getBrowser: function() {
        var e = navigator.appName, t = navigator.userAgent, n;
        var r = t.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
        if (r && ( n = t.match(/version\/([\.\d]+)/i)) !== null) {
            r[2] = n[1];
        }
        r = r ? [r[1], r[2]] : [e, navigator.appVersion, "-?"];
        return r[0];
    },
    
    setContainers: function() {
        
        var $pageInnerRight = $('.page-inner.layout-right');
        var $sidebar = $('#sidebar');
        
        if( $pageInnerRight.length && $sidebar.length ) {
            if( $pageInnerRight.outerHeight() < $sidebar.outerHeight() ) {
                $pageInnerRight.css('min-height', $sidebar.outerHeight() );
            }
        }
        
        var $postInnerRight = $('.layout-right .journal-content');
        
        if( $postInnerRight.length && $sidebar.length ) {
            if( $postInnerRight.outerHeight() < $sidebar.outerHeight() ) {
                $postInnerRight.css('min-height', $sidebar.outerHeight() );
            }
        }
        
        var $bwLine = $('.bw-line');
        
        if( $bwLine.length && $sidebar.length ) {
            if( $bwLine.height() < $sidebar.outerHeight() ) {
                $bwLine.css('min-height', $sidebar.outerHeight() );
            }
        }
        
    },
    
    stickySidebar: function() {
        
        if( $('#sidebar').height() >= $('#content').height()) { return; }
        
        $(window).on('mousewheel', function(e, d){
            if( d < 0 && ! $('#sidebar').hasClass('go-sticky') ) {
                sidebarRealPosition = $('#sidebar').outerHeight() + $('#sidebar').offset().top;
            }
        });
        
        $(window).scroll(function() {
            App.checkStickySidebar();
        });
    },
    
    checkStickySidebar: function() {
        
        var $sidebar = $('#sidebar'),
            $goSticky = $('#sidebar.go-sticky'),
            $sticky = $('#sidebar.sticky');
        
        var windowBottom = $(window).scrollTop() + $(window).height();
        var sidebarBottom = $sidebar.outerHeight() + $sidebar.offset().top;
        
        if( ! $sidebar.hasClass('go-bottom')) {
            var sidebarBottomPosition = $('#footer').offset().top - sidebarOffsetTop - $('.sidebar-inner', $sidebar).height();
        }
        
        if( windowBottom > sidebarRealPosition && sidebarRealPosition > 0 ) {
            if( ! $sticky.hasClass('go-sticky') ) {
                $('.sidebar-inner', $sidebar).css('width', $sticky.width());
                $sticky.addClass('go-sticky');
            }
        }else{
            if( $sticky.hasClass('go-sticky') ) {
                $sticky.removeClass('go-sticky');
            }
        }
        
        if( windowBottom > $('#footer').offset().top ) {
            $goSticky.addClass('go-bottom');
            $('.sidebar-inner', $goSticky).css('top', sidebarBottomPosition);
        }else{
            $goSticky.removeClass('go-bottom');
            $('.sidebar-inner', $goSticky).css('top', 'auto');
        }
    },
    
    stickyHeader: function() {
        
        $(window).scroll(function() {
            App.checkSticky();
        });
        
    },
    
    checkSticky: function() {
        
        if( $(window).scrollTop() > headerPosition) {
            if( ! $('#header.sticky').hasClass('go-sticky') ) {
                $('#header.sticky').addClass('go-sticky');
                setTimeout(function() {
                    $('#header.sticky').addClass('slide-down');
                }, 20);
            }
        }else if( $(window).scrollTop() < headerRealPosition ) {
            if( $('#header.sticky').hasClass('go-sticky') ) {
                $('#header.sticky').removeClass('go-sticky slide-down');
            }
        }
    },
    
    onLoad: function() {
        
        if( $('#sidebar.sticky').length ) {
            sidebarRealPosition = $('#sidebar').length ? $('#sidebar').outerHeight() + $('#sidebar').offset().top : 0;
            App.checkStickySidebar();
        }
        
        setTimeout(function() {
            if( ! $('#sidebar').hasClass('go-sticky') ) {
                sidebarRealPosition = $('#sidebar').length ? $('#sidebar').outerHeight() + $('#sidebar').offset().top : 0;
            }
        }, 1000);
        
    },
    
    onReady: function() {
        
        $(document).ready(function() {
            if( ! $('#ip-container').length ) {
                App.checkSticky();
            }
        });
        
    },
    
    onSmartResize: function() {
        
        jQuery(window).on("debouncedresize", function() {
            
            App.setContainers();
            
            // calculate header position
            if( $('#header.sticky').length ) {
                headerPosition = calculateHP();
                headerRealPosition = calculateHRP();
            }
            
        });
        
    },
    
    pace: function() {
        
        if( $body.hasClass( 'bw-progress-preloader' ) ) {
            Pace.on('hide', function() {
                
                TweenMax.to($('#preloader-mask'), 0.3, {alpha:0, onComplete: function() {
                    $('#preloader-mask').remove();
                }});
                
            });
        }
        
    },
    
    magnificPopup: function() {
        
        $(".mp-item, .post a[href$='.jpg'], .post a[href$='.jpeg'], .post a[href$='.png'], .post a[href$='.gif'], .page a[href$='.jpg'], .page a[href$='.jpeg'], .page a[href$='.png'], .page a[href$='.gif']").magnificPopup({
            removalDelay: 500,
            mainClass: 'mfp-fade',
            type: 'image',
            image: {
                titleSrc: function(item) {
                    var output = '';
                    if (typeof item.el.attr('data-title') !== "undefined" && item.el.attr('data-title') !== "") {
                        output = item.el.attr('data-title');
                    }
                    if (typeof item.el.attr('data-alt') !== "undefined" && item.el.attr('data-alt') !== "") {
                        output += '<small>' + item.el.attr('data-alt') + '</small>';
                    }
                    return output;
                }
            },
            iframe: {
                markup:
                    '<div class="mfp-figure mfp-figure--video">' +
                    '<button class="mfp-close"></button>' +
                    '<div>' +
                    '<div class="mfp-iframe-scaler">' +
                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mfp-bottom-bar">' +
                    '<div class="mfp-title mfp-title--video"><small></small></div>' +
                    '<div class="mfp-counter"></div>' +
                    '</div>' +
                    '</div>',
                patterns: {
                    youtube: {
                        index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
                        id: 'v=', // String that splits URL in a two parts, second part should be %id%
                        // Or null - full URL will be returned
                        // Or a function that should return %id%, for example:
                        // id: function(url) { return 'parsed id'; }
                        src: '//www.youtube.com/embed/%id%' // URL that will be set as a source for iframe.
                    },
                    vimeo: {
                        index: 'vimeo.com/',
                        id: '/',
                        src: '//player.vimeo.com/video/%id%'
                    },
                    gmaps: {
                        index: '//maps.google.',
                        src: '%id%&output=embed'
                    }
                    // you may add here more sources
                },
                srcAction: 'iframe_src' // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
            },
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                // arrowMarkup: '<a href="#" class="mfp-arrow mfp-arrow-%dir% control-item arrow-button arrow-button--%dir%"></a>',
                tPrev: 'Previous (Left arrow key)', // title for left button
                tNext: 'Next (Right arrow key)', // title for right button
                // tCounter: '<div class="gallery-control gallery-control--popup"><div class="control-item count js-gallery-current-slide"><span class="js-unit">%curr%</span><sup class="js-gallery-slides-total">%total%</sup></div></div>'
                tCounter: '<div class="gallery-control gallery-control--popup"><a href="#" class="control-item arrow-button arrow-button--left js-arrow-popup-prev"></a><div class="control-item count js-gallery-current-slide"><span class="js-unit">%curr%</span> / <span class="js-gallery-slides-total">%total%</span></div><a href="#" class="control-item arrow-button arrow-button--right js-arrow-popup-next"></a></div>'
            },
            callbacks: {
                markupParse: function(template, values, item) {
                    values.title = item.el.attr('data-title') + '<small>' + item.el.attr('data-alt') + '</small>';
                }
            },
        }
        );
    },
    
    isotope: function() {
        
        var $mixer = $('#mixer:not(.no-isotope), .grid-mixer');
        
        if ( document.querySelector('body').offsetHeight > window.innerHeight ) {
            document.documentElement.style.overflowY = 'scroll';
        }
        
        // init isotope
        if( $mixer.length > 0 ) {
            
            $mixer.imagesLoaded(function() {
                $mixer.isotope({
                    itemSelector: '.mix-item'
                });
                setTimeout(function() {
                    $mixer.isotope( 'reloadItems' ).isotope();
                }, 100);
            });
            
        }
        
    },
    
    cartPut: function( cartNumber ) {
        
        var $cart = $('.cart-count');
        var currentCartNumber = parseInt( $cart.html() );
        
        if( cartNumber > currentCartNumber ) {
            
            $cart.html( cartNumber );
            $cart.removeClass( 'zoomOut' ).addClass( 'animated bounceIn' );
            
            $('.cart-notification').fadeIn(300);
            clearTimeout( removeNot );
            removeNot = setTimeout(function() {
                $('.cart-notification').fadeOut(300);
            }, 6000);
            
        }
        
    },
    
    bind: function() {
        
        // disable empty url\'s
        jQuery(document).on('click', 'a[href="#"]', function(e) {
            e.preventDefault();
        });
        
        // search main
        $('#peliegro-search .search-submit').attr('value','Go');
        
        // peliegro search toggle
        $('#header .header-search i, #peliegro-search .bw-close').bind('click', function() {
            $('body').toggleClass('bw-expand-search');
            if( $('body').hasClass('bw-expand-search') ) {
                setTimeout(function() {
                    $('body').addClass('bw-search-do');
                    $('html').css('overflow-y', 'hidden');
                    $('#peliegro-search .search-field').focus();
                }, 100);
            }else{
                $('body').removeClass('bw-search-do');
                $('html').css('overflow-y', 'scroll');
            }
        });
        
        // mobile toggle
        $('#mobile-toggle').bind('click', function() {
            
            var $body = $('body');
            var windowHeight = $(window).height() - 1;
            
            $body.toggleClass( 'mobile-expand-menu' );
            
            if($('#wpadminbar')) {
                windowHeight -= $('#wpadminbar').height();
            }
            
            if( $body.hasClass( 'mobile-expand-menu' ) ) {
                $('body').height( windowHeight ).addClass('overflow');
            }else{
                $('body').height( 'auto' ).removeClass('overflow');
            }
            
        });
        
        // back to top button
        $('.back-top').on('click', function() {
            $('html,body').animate({scrollTop: 0}, 1200, 'easeInOutCubic');
        });
        
        // social icons hover effect
        $('.social a').bind('mouseenter', function() {
            TweenLite.to($('.social-holder .pad'), 0.4, {opacity: 1, left: $(this).position().left + 1, ease: Back.easeOut});
        }).bind('mouseleave', function() {
            TweenLite.to($('.social-holder .pad'), 0.4, {opacity: 0});
        });
        
        // alert close button
        $('.alert .close').bind('click', function() {
            $(this).closest('.alert').remove();
        });
        
        // section load more
        $( '.section-load-more' ).each(function( i, el ) {
            
            var $el = $( el ),id = $( el ).attr( 'id' );
            $el.find('.load-more-btn').css('opacity', 1);
            
            $(el).on( 'click', '.load-more > a', function( e ) {
                
                e.preventDefault();
                
                var $clicked = $(this),
                    $new_archive = $( '<div/>' ),
                    elIndex = $el.index('.section-load-more');
                
                $el.find( '.load-more' ).find( '.fa-refresh' ).addClass('fa-spin');
                
                $clicked.css('opacity', 0);

                $new_archive.load( $clicked.attr( 'href' ) + ' #' + id + ':first', undefined, function(response) {
                    
                    $new_archive = $(response).find( '.section-load-more' );
                    
                    var $new_items_clicked = $new_archive.eq(elIndex);
                    
                    $clicked.replaceWith($new_items_clicked.find('.load-more'));
                    
                    // visual composer animation effect fix
                    $new_items_clicked.addClass( 'wpb_start_animation' );
                    
                    $new_items_clicked.imagesLoaded(function() {
                        
                        var $toAppend = $new_items_clicked.find('.section-append').children();
                        $el.find('.section-append').append( $toAppend ).isotope( 'appended', $toAppend ).isotope( 'reloadItems' ).isotope({filter: '*'});
                        $('.section-append').isotope({filter: '*'});
                        
                        if( $('.mixer-holder.layout-sidebar').length ) {
                            $('html,body').stop(false,false).animate({scrollTop: $('.mixer-holder').height()}, 600, 'easeInOutCubic');
                        }
                        
                        var $loadMoreBtn = $el.find('.load-more-btn');
                        
                        if( $loadMoreBtn.length ) {
                            $loadMoreBtn.css('opacity', 1);
                        }else{
                            $el.find('.load-more').html('<span class="load-more-placeholder"><i class="fa fa-check"></i></span>');
                            setTimeout(function() {
                                $el.find('.load-more-placeholder').css('opacity', 0.12);
                            }, 2000);
                        }
                        
                    });
                    
                    // select "all"
                    var $filterEl = $el.prev('.latest-posts-filter');
                    $filterEl.find('li').removeClass('active');
                    $('li:first', $filterEl).addClass('active');
                    
                });
            });
        });
        
    },
    
    getHashFromUrl: function(url) {
        var a = document.createElement("a");
        a.href = url;
        return a.hash.replace(/^#/, "");
    },
    
    slider: function() {
        
        var $sliderElement = $('.bw-slider');
        
        if($sliderElement.length > 0) {
            $sliderElement.each(function() {
                
                var self = $(this);
                
                var carouselOptions = {
                    
                    loop:               ( self.hasClass('slider-loop') && $('> *', self).length > 1) ? true : false,
                    items:              self.attr('data-number') ? parseInt(self.attr('data-number')) : 1,
                    autoplay:           self.hasClass('auto-play') ? 4000 : false,
                    autoplayTimeout:    3000,
                    autoHeight:         self.hasClass('auto-height') ? true : false,
                    animateIn:          self.attr('data-animate-in') ? self.attr('data-animate-in') : false,
                    animateOut:         self.attr('data-animate-out') ? self.attr('data-animate-out') : false,
                    lazyLoad:           true,
                    nav:                self.hasClass('has-nav') ? true : false,
                    dots:               self.hasClass('has-pag') ? true : false,
                    navigationText:     ['',''],
                    video:              true,
                    responsive:         true
                    
                };
                
                if( self.hasClass('related-slider') ) {
                    carouselOptions.responsive = {
                        0 : {
                            items : 1
                        },
                        480 : {
                            items : 1
                        },
                        640 : {
                            items : 2
                        },
                        960 : {
                            items : 3
                        }
                    };
                }
                
                self.imagesLoaded(function() {
                    self.owlCarousel(carouselOptions);
                });
                
            });
        }
        
    },
    
};

$(document).ready(function() {
    App.start();
});
