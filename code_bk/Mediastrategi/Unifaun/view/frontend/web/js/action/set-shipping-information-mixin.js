/* global define*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quoteModel) {
    'use strict';
    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quoteModel.shippingAddress();

            // console.log('Mediastrategi Unifaun - set-shipping-information-mixin - quoteModel:');
            // console.log(quoteModel);

            if (shippingAddress.extensionAttributes === undefined) {
                shippingAddress.extensionAttributes = {};
            }

            var shippingMethod = quoteModel.shippingMethod();
            var shippingMethodCode = shippingMethod.method_code;

            // Custom Pick Up Agent
            if (typeof(window.msunifaun_selectedAgent) !== 'undefined' &&
                typeof(window.msunifaun_selectedAgent[shippingMethodCode]) === 'object'
               ) {
                var pickUpLocationModel = window.msunifaun_selectedAgent[shippingMethodCode];
                if (typeof(pickUpLocationModel.id) !== 'undefined'
                    && pickUpLocationModel.id
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_id = pickUpLocationModel.id;
                }
                if (typeof(pickUpLocationModel.name) !== 'undefined'
                    && pickUpLocationModel.name
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_name = pickUpLocationModel.name;
                }
                if (typeof(pickUpLocationModel.city) !== 'undefined'
                    && pickUpLocationModel.city
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_city = pickUpLocationModel.city;
                }
                if (typeof(pickUpLocationModel.address1) !== 'undefined'
                    && pickUpLocationModel.address1
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_address1 = pickUpLocationModel.address1;
                }
                if (typeof(pickUpLocationModel.zipcode) !== 'undefined'
                    && pickUpLocationModel.zipcode
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_zip_code = pickUpLocationModel.zipcode;
                }
                if (typeof(pickUpLocationModel.countryCode) !== 'undefined'
                    && pickUpLocationModel.countryCode
                   ) {
                    shippingAddress.extensionAttributes.pick_up_location_country = pickUpLocationModel.countryCode;
                }
            }

            // Update address
            quoteModel.shippingAddress(shippingAddress);

            // console.log('Mediastrategi Unifaun - set-shipping-information-mixin - shippingAddress after:');
            // console.log(quoteModel.shippingAddress());
            return originalAction();
        });
    };
});
