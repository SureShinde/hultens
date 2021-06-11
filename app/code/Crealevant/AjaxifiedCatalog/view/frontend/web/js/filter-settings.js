define([
        "jquery",
    ],
    function($) {
        "use strict";
        function filterOptionsMenu() {
            var countItems = $('.filter-options').children('.filter-options-content').length;
            // Filter Options Items
            var filterOptionsDropdown = $('.filter-options').children('.filter-dropdown');
            // Filter Options Menu Items
            var filterOptionsMenuItems = $('.filter-options').children('.desktop-menu-wrapper').children('.filter-options-menu.sidebar').children('.filter-menu-content');
            // Add HTML For Filter Menu Sidebar
            var filterOptionsMenuSidebar = $('.filter-options-menu.sidebar');
            var filterOptionsMenuSidebarFilterItem = $(filterOptionsMenuItems).children('.items').children('.item');
            var boxWidth = $(filterOptionsMenuSidebar).width();
            var filterOptionsMenuHeader = $(filterOptionsMenuSidebar).children('.filter-header');
            var closeFilterOptionsMenu = $(filterOptionsMenuHeader).children('.close-filter-menu');
            // Move all filter options menu content
            $(filterOptionsMenuItems).appendTo('.filter-options-menu.sidebar');


            // Add Click Event For Close Menu
            $(closeFilterOptionsMenu).on('click', function () {
                filterOptionsMenuSidebar.hide();
            });
            // Add Click Event For Filter Options Menu
            $(filterOptionsMenuItems).children('.filter-options-title').on('click', function () {
                var toggleContent = $(this).parent().children('.items');
                var checkbox = $(this).parent().children('.items').children('.item').children('.filter-link');
                var isVisible = toggleContent.is(":visible");
                // Hide All Filter Dropdown Items
                filterOptionsMenuItems.children('.items').hide();
                filterOptionsMenuItems.children('.filter-options-title').removeClass('active');
                // If Current Filter Dropdown Items isn't showing, show it
                if (!isVisible) {
                    toggleContent.show();
                    toggleContent.parent().children('.filter-options-title').addClass('active');
                }
            });
            $(filterOptionsMenuSidebarFilterItem).on('click', function () {
                $(this).children("#cb1").attr("checked", "checked");
            });
            $(filterOptionsMenuSidebarFilterItem).on('mouseover', function () {
                $(this).children('a').children('#cb1').addClass('active');
            });
        }
        function filterMenuPositionSwitcher (){
            var mediaScreen = $(window).width();
            var filterButton = "";
            var menuWrapper = "";
            if(mediaScreen > 1024){
                var filterButton = $('.filter-btn.desktop');
                var menuWrapper = $('.desktop-menu-wrapper');

            }
            else{
                var filterButton = $('.filter-btn.mobile');
                var menuWrapper = $('.mobile-menu-wrapper');
            }
            var filterOptionsMenuSidebar = $('.filter-options-menu.sidebar');
            var filterMenuContent = $('.filter-options').children('.filter-menu-content');
            var filterOptionsDropdown = $('.filter-options').children('.filter-options-content.filter-dropdown');

            // Move Filter Items To Sidebar
            if(mediaScreen > 1024){
                filterOptionsDropdown.appendTo('.filter-options'); // Move items back to not have to reload when resizeing
                filterMenuContent.appendTo(filterOptionsMenuSidebar);

                filterOptionsMenuSidebar.children('.filter-menu-content.second').appendTo('.filter-options').removeClass('filter-menu-content').addClass('filter-dropdown');
                // Move Filter Options Menu Sidebar To Desktop Menu wrapper
                filterOptionsMenuSidebar.appendTo('.desktop-menu-wrapper');
            }
            else{
                filterMenuContent.appendTo(filterOptionsMenuSidebar);
                filterOptionsDropdown.addClass('filter-menu-content').addClass('second');
                filterOptionsDropdown.appendTo(filterOptionsMenuSidebar).removeClass('filter-dropdown');
            }
        }
        function filterMenuToggleSwitcher() {
            var mediaScreen = $(window).width();


            //  Filter Options Dropdown
            // ____________________________
            var filterOptionsDropdown = $('.filter-options').children('.filter-dropdown');
            // Add Click Event For Filter Options Dropdown
            $(filterOptionsDropdown).children('.filter-options-title').on('click', function () {
                var toggleContent = $(this).parent().children('.items');
                var isVisible = toggleContent.is(":visible");

                // Hide All Filter Dropdown Items
                filterOptionsDropdown.children('.items').hide();
                filterOptionsDropdown.children('.filter-options-title').removeClass('active');
                // If Current Filter Dropdown Items isn't showing, show it
                if (!isVisible) {

                    toggleContent.show();
                    toggleContent.parent().children('.filter-options-title').addClass('active');
                }
            });

            //
            //  Filter Options Menu Sidebar
            // ____________________________

            // Filter Classes
            var filterOptionsMenuSidebar = $('.filter-options-menu.sidebar');
            var boxWidth = $(filterOptionsMenuSidebar).width();
            var filterOptionsMenuHeader = $(filterOptionsMenuSidebar).children('.filter-header');
            var closeFilterOptionsMenu = $(filterOptionsMenuHeader).children('.close-filter-menu');
            var filterOptionsMenuItem = $('.filter-menu-content');
            var filterOptionsMenuItems = $('.filter-options').children('.desktop-menu-wrapper').children('.filter-options-menu.sidebar').children('.filter-menu-content');
            var filterItemSidebar = $(filterOptionsMenuItems).children('.items').children('.item');
            var totalProducts = $('.toolbar.toolbar-products').eq(0).children('.toolbar-amount').children('.toolbar-number').text();

            var filterMenuContent = $('.filter-options').children('.filter-menu-content');
            var filterButton = "";
            var menuWrapper = "";
            if(mediaScreen > 1024){
                var filterButton = $('.filter-btn.desktop');
                var menuWrapper = $('.desktop-menu-wrapper');
            }
            else{
                var filterButton = $('.filter-btn.mobile');
                var menuWrapper = $('.mobile-menu-wrapper');
            }

            // Set Total Products Text To button in sidebar menu
            $(filterOptionsMenuSidebar).children('.total-products').children('span').text(totalProducts);
            // Add Click Event To Open Filter Menu
            $(filterButton).on('click', function () {
                var toggleContent = $(this).parent().children('.filter-options-menu.sidebar');
                var isVisible = toggleContent.is(":visible");
                $(filterOptionsMenuSidebar).hide().removeClass('hide').removeClass('actived');
                // If Current Filter Dropdown Items isn't showing, show it
                if (!isVisible) {
                    $(filterOptionsMenuSidebar).addClass('actived').children('.filter-menu-content').removeClass('hide').show();
                }
                event.preventDefault();
            });
            // Add Click Event To Close Filter Menu
            $(closeFilterOptionsMenu).on('click', function () {
                filterOptionsMenuSidebar.hide().removeClass('actived');
            });

            // Add Click Event For Filter Options Menu
            $('.filter-menu-content').children('.filter-options-title').on('click', function () {
                var newToggle = $(this).parent().children('.items');
                var isVisible = newToggle.is(":visible");
                // Hide All Filter Dropdown Items
                filterOptionsMenuItem.children('.filter-options-title').removeClass('active');
                filterOptionsMenuItem.children('.items').hide();

                // If Current Filter Dropdown  Items isn't showing, show it
                if (!isVisible) {
                    newToggle.show();
                    newToggle.parent().children('.filter-options-title').addClass('active');
                }
            });
        }
        filterMenuToggleSwitcher();
        filterMenuPositionSwitcher();
        $('body').on('contentUpdated', function () {
            filterMenuPositionSwitcher();
            //currentPageValue();
            filterMenuToggleSwitcher();
        });
        return;
    });