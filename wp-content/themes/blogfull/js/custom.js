(function($) {
    "use strict";
    function newsticker(){ 
        var ticker =$( ".bs-latest-news" );
        var mainDiv =$('.bs-latest-news-slider');
        var tickerSlide = mainDiv.marquee({
            speed: 50,
            direction:'left', 
            delayBeforeStart: 0,
            duplicated: true,
            pauseOnHover: false,
            startVisible: true
        });
        ticker.on( "click", ".bs-latest-play span", function() {
            $(this).find( "i" ).toggleClass( "fa-pause fa-play" )
            tickerSlide.marquee( "toggle" );
        })
    }
    newsticker();
    
    /* =================================
    ===        home -slider        ====
    =================================== */
    function homemainTwo() {
        var homemain = new Swiper('.homemain-two', {
        direction: 'horizontal',
        loop: false,
        autoplay: true,
        speed: 700,
        slidesPerView: 1,
        spaceBetween: 20,
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            }		
        }
    
        });              
    }
    homemainTwo(); 
})(jQuery);