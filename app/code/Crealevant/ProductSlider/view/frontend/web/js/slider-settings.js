define([
    "jquery"
],
    function($) {
    "use strict";

    function CmsHomeSliders() {
        // Settings for slider Margin Value Change Depending On Media Screen
        var marginValue = "";
        var mediaScreen = $(window).width();

        if (mediaScreen < 992){
            var marginValue = 0;
        }
        if (mediaScreen > 992){
            var marginValue = 10;
        }
            //Slider - home page.
            $(".cms-home-slider").owlCarousel({
                loop:true,
                autoWidth:false,
                nav: false,
                navText: [
                    "<i class='icon hu-left-icon'></i>",
                    "<i class='icon hu-right-icon'></i>"
                ],
                responsive: {
                    0: {
                        items: 2,
                        margin: 0
                    },
                    768: {
                        items: 3,
                        margin: marginValue
                    },
                    1024: {
                        items: 6,
                        margin: marginValue
                    },
                    1300: {
                        items: 6,
                        margin: marginValue
                    }
                }
            });
            //Slider - Home Page Category Links
            $(".category-links-wrapper").owlCarousel({
                stagePadding: 42,
                loop:true,
                margin:0,
                nav: false,
                autoWidth:false,
                navText: [
                    "<i class='icon hu-left-icon'></i>",
                    "<i class='icon hu-right-icon'></i>"
                ],
                responsive: {
                    0: {
                        items: 2
                    },
                    500: {
                        items: 3
                    },
                    692: {
                        items: 4
                    },
                    992: {
                        items: 5
                    },
                    1000: {
                        items: 3
                    },
                    1300: {
                        items: 6
                    },
                    1600: {
                        items: 6
                    }
                }
            });
        }
        CmsHomeSliders();
        return;
    });