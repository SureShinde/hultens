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
                template: 'Magento_Checkout/minicart-tax',
                activeMethod: ''
            },

            getOffer: function() {

            },

            offerExists: function() {

            },

            showOffer: function () {
            },

            getValue: function() {
                return totals().cart().tax_total
            }
        });
    }
);
