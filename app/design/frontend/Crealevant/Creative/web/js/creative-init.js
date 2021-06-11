define([
        "jquery",
        "js/jquery.nice-select",
        'Crealevant_Lookbook/js/owl.carousel'
    ],
    function($) {
        "use strict";

        function moveKlarnaPlacement(){
            var mediaScreen = $(window).width();
            if (mediaScreen < 1024){
                if($('body').hasClass('page-product-bundle')){
                    $('.cms-info').prependTo('.product-details');
                }
            }
        }

        moveKlarnaPlacement();
        function campaignBannerPosition() {
            if ($('body').hasClass('catalog-product-view')) {

                var mediaScreen = $(window).width();
                var header = $('.page-header');
                var headerHeight = $(header).children('.header.content').height();
                var campaignBannerHeight = $(header).children('.campaign-wrapper').height();
                var breadCrumbsHeight = 31;
                var campaignBannerPosition = headerHeight + breadCrumbsHeight;
                var flagContainer = $('.product.media').children('.flag-container-prod');
                if (mediaScreen < 1024) {

                    $(flagContainer).animate({'top': campaignBannerHeight - 20}, '250');
                    $(header).children('.campaign-wrapper').animate({'top': campaignBannerPosition}, '250');
                    // Set top position for campaign wrapper depending of the heights from header + campaign banner and margins.
                }
                if (mediaScreen >= 1024) {
                    // Reset campaign wrapper css to default.
                    $(flagContainer).css('top', '');
                    $(header).children('.campaign-wrapper').css('top', '');
                }
            }
        }
        $(window).on('resize', function () {
            campaignBannerPosition();
        });
        campaignBannerPosition();
        function productAddedOpenMinicart() {
            $('.action.tocart.primary').on('click', function () {
                var page = $('html, body');
                var animation = true;
                var dataBlockMinicart =  $('[data-block="minicart"]');
                $(dataBlockMinicart).on('contentLoading', function () {
                    $(dataBlockMinicart).on('contentUpdated', function () {
                        if($(animation)) {
                            animation = false;
                            $(page).stop().animate({
                                scrollTop: 0
                            }, 500, function () {
                                animation = true
                                $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog("open");
                            });
                        }
                    });
                });
            });
        }
        productAddedOpenMinicart();

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

        // Switcher Product Item Photo
        function switchImage() {
            $('.product-item-photo').mouseover(function () {
                var hoveredImage = $(this).children('.is-image');
                var altImage = hoveredImage.attr('data-value');
                var altImageEle = $(this).children().children().children('img.product-image-photo');
                altImageEle.attr('src', altImage);
            });
            $('.product-item-photo').mouseleave(function () {
                var hoveredImage = $(this).children().children().children('img.product-image-photo');
                var altImage = hoveredImage.attr('data-original');
                var altImageEle = $(this).children().children().children('img.product-image-photo');
                altImageEle.attr('src', altImage);
            });
        }
        switchImage();
        // CMS Page View Month Tab
        $('.month-tab').on('click', function () {
            var toggleContent = $(this).children('.month-content');
            var monthContent = $('.month-content');
            var isVisible = toggleContent.is(":visible");
            // Hide All Month Content
            monthContent.hide("slow");
            // If Current Month Content  isn't showing, show it
            if (!isVisible) {
                toggleContent.show("slow");
            }
        });
        // Catalog Product View More Info Details Tab
        $('.tab-title').on('click', function () {
            var mediaScreen = $(window).width();
            if(mediaScreen < 1024) {
                var toggleContent = $(this).parent().children('.tab-content');
                var tabContent = $('.tab-content');
                var isVisible = toggleContent.is(":visible");
                // Hide All Month Content
                tabContent.hide("slow").parent().children('.tab-title').removeClass('opened');
                // If Current Month Content  isn't showing, show it
                if (!isVisible) {
                    toggleContent.show("slow").parent().children('.tab-title').addClass('opened');
                }
            }
        });
        if ($('body').hasClass('catalog-product-view')) {
            var mediaScreen = $(window).width();
            if(mediaScreen < 1024) {
                $('.data.item.title').on('click', function () {
                    var toggleContent = $(this).parent().children('.data.item.content');
                    var tabContent = $('.data.item.content');
                    var isVisible = toggleContent.is(":visible");
                    $('.data.item.title:first-child').removeClass('first');
                    // Hide All Month Content
                    tabContent.hide("slow").parent().children('.data.item.title').removeClass('opened');
                    // If Current Month Content  isn't showing, show it
                    if (!isVisible) {
                        toggleContent.show("slow").parent().children('.data.item.title').addClass('opened');
                    }
                });
                $('.data.item.content').hide("slow");
                $('.data.item.title:first-child').addClass('first');
            }
        }
        // Check if product options is true
        function customisableProductOptions (){
            var findCustomisableOption = $('body').find('.product-options-wrapper').find('.mageworx-option-container');
            //Setter for CustomisableOption
            if (findCustomisableOption.length == true){
                var priceContainer = $('.product-info-price');
                $('body').addClass('product-with-custom-options');
                $(priceContainer).insertAfter('.product-options-wrapper');
            }
        }
        customisableProductOptions();
        //Classes that should use niceSelect
        $('select.bundle-option-select').niceSelect(); //Bundle options ( Select )
        $('select.qty-dpd').niceSelect(); //Quanity dropdown ( Select )
        $('.product-custom-option').niceSelect();
        // Header - Country Dropdown on dimensionsChanged
        $(".country-dropdown").on("dimensionsChanged", function (event, data) {
            var opened = data.opened;
            var panelWrapper = $(this).parent().parent().parent();
            if (opened) {
                // Set z-index bigger than header content
                panelWrapper.css('z-index', '13');
                // do something when the content is opened
                return;
            }
            // Rester z-index to normal when its closed.
            panelWrapper.css('z-index', '');
        });

        function minicartDropdownChecker() {
            var headerContent = $('.header.content');
            $('[data-block="minicart"]').on('dropdowndialogopen', function () {
                // Set z-index bigger than Panel Wrapper
                headerContent.css('z-index', '13');
                $('.customer-welcome').removeClass('active');
                $('.customer-name').removeClass('active');

            });
            $('[data-block="minicart"]').on('dropdowndialogclose', function () {
                // Reset Z-index
                headerContent.css('z-index', '');
            });
        }
        // Execute Minicart Dropdown Checker
        minicartDropdownChecker();

        // Header - Minicart Content
        function scrollToBottomDescription() {
            if ($('body').hasClass('cms-page-view')) {
                $(".scroll-to-desc").click(function () {
                    var breadCrumbsOffset = $(".breadcrumbs").offset().top - 70;
                    // Offset ( Position of element + height of header fixed + extra space
                    $([document.documentElement, document.body]).animate({
                        scrollTop: breadCrumbsOffset
                    }, 400);
                });
            }
            // Check If Page Is Category Page
            if ($('body').hasClass('catalog-category-view')) {
                // Set cookie when clicking on pagination
                if ($(window).width() < 992) {
                    $(".scroll-to-desc").click(function () {
                        var categoryBottomDescriptionPosition = $(".category-view").offset().top + 300;
                        // Offset ( Position of element + height of category-top-wrapper - padding of element description-inner )
                        $([document.documentElement, document.body]).animate({
                            scrollTop: categoryBottomDescriptionPosition
                        }, 400);
                    });
                }
                else {
                    $(".scroll-to-desc").click(function () {
                        var categoryBottomDescriptionPosition = $(".category-view").offset().top - 60;
                        $([document.documentElement, document.body]).animate({
                            scrollTop: categoryBottomDescriptionPosition
                        }, 400);
                    });
                }
            }
        }
        // Executes the scrollToBottomDescription function on page load
        scrollToBottomDescription ();

        // Minicart Content Increase/Decrease
        function minicartIncreaseDecrease() {
            $('.minicart-wrapper').on('click', '.cart-btn-plus', function () {
                // Current Cart Item Qty
                var cartItemQty = $(this).parent().children().siblings('input');
                // Current Update Cart Item Button
                var updateCartItem = $(this).parent().children('.update-cart-item');
                // Get the value from cart item qty and change the value + 1
                cartItemQty.val(parseInt(cartItemQty.val()) + 1);
                // After cart item qty is change trigger keyup
                cartItemQty.trigger('keyup');

                if (cartItemQty.val() > 1) {
                    $(this).parent().children('.cart-btn-minus').removeAttr('disabled');
                }
                // Click on the update cart item
                updateCartItem.trigger('click');
            });
            $('.minicart-wrapper').on('click', '.cart-btn-minus', function () {
                // Current Cart Item Qty
                var cartItemQty = $(this).parent().children().siblings('input');
                // Current Update Cart Item Button
                var updateCartItem = $(this).parent().children('.update-cart-item');

                if (cartItemQty.val() < 3) {
                    $(this).prop('disabled', true);
                }
                cartItemQty.val(parseInt(cartItemQty.val()) - 1);
                // After cart item qty is change trigger keyup
                cartItemQty.trigger('keyup');
                // Click on the update cart item
                updateCartItem.trigger('click');
            });
        }
        minicartIncreaseDecrease();

        // Catalog Product View Qty + / -
        $('.btn-number').on('click', function (e) {
            e.preventDefault();
            var fieldName = $(this).attr('data-field');
            var type = $(this).attr('data-type');
            var input = $("#" + fieldName);
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                if (type == 'minus') {
                    if (currentVal > input.attr('min')) {
                        input.val(currentVal - 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('min')) {
                        $(this).attr('disabled', true);
                    }

                } else if (type == 'plus') {

                    if (currentVal < input.attr('max')) {
                        input.val(currentVal + 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('max')) {
                        $(this).attr('disabled', true);
                    }

                }
            } else {
                input.val(0);
            }
        });
        $('body').on('contentUpdated', function () {
            // Execute function switch image (hover change image)
            switchImage();
            // Executes the scrollToBottomDescription function on page load
            scrollToBottomDescription();
            customisableProductOptions();
        });

        return;
    });