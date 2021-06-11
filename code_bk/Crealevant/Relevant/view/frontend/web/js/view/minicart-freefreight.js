/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        "Magento_Tax/js/view/checkout/minicart/subtotal/totals",
        "underscore",
        'uiComponent'
    ],
    function (
        totals,
        _,
        Component
    ) {

        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/minicart-freefreight',
                activeMethod: ''
            },

            getOffer: function() {

            },

            offerExists: function() {

            },

            showOffer: function () {
            },

            getValue: function() {
                if(totals().cart().minicart_freefreight) {
                    var price = totals().cart().minicart_freefreight;
                } else if(typeof window.checkoutConfig !== 'undefined' && typeof window.checkoutConfig.amount_left !== 'undefined') {
                    var a_left = window.checkoutConfig.amount_left;
                    if (a_left == null) {
                        var price = "<span class='price'>0 kr</span>";
                    } else {
                        var price = "<span class='price'>" + a_left + " kr</span>";
                    }
                } else {
                    var a_left = window.checkout.amount_left;
                    if (a_left == null) {
                        var price = "<span class='price'>0 kr</span>";
                    } else {
                        var price = "<span class='price'>" + a_left + " kr</span>";
                    }
                }
                return price;
            }
        });
    }
);
