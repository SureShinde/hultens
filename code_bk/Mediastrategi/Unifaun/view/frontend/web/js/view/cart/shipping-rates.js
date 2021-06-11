/** @see Magento_Checkout/js/view/cart/shipping-rates */
/* global define */
define([
    'jquery',
    'ko',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/new-customer-address',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/action/select-shipping-method',
], function (
    $,
    ko,
    wrapper,
    quoteModel,
    selectShippingAddress,
    newCustomerAddress,
    shippingService,
    setShippingInformationAction,
    selectShippingMethodAction
) {
    'use strict';

    var mixin = {
        initialize: function()
        {
            this._super();
            // console.log('Mediastrategi Unifaun - cart shipping-rates initialize()');
            // console.log(this);
            // console.log(this.shippingRates());

            var parent = this;

            this.customPostCode = ko.observable('');
            this.showZipCodePicker = (typeof(window.msunifaun_showZipCodePicker) !== 'undefined'
                                      && window.msunifaun_showZipCodePicker);
            this.showCountryPicker = (typeof(window.msunifaun_showCountryPicker) !== 'undefined'
                                      && window.msunifaun_showCountryPicker);
            this.showCountryPickerList = (typeof(window.msunifaun_showCountryPickerList) !== 'undefined'
                                          ? window.msunifaun_showCountryPickerList
                                          : false);
            var selectedCountry = false;
            var shippingAddress = quoteModel.shippingAddress();
            if (shippingAddress
                && shippingAddress.countryId
               ) {
                selectedCountry = shippingAddress.countryId;
            }

            if (this.showCountryPickerList) {
                var newCountries = [];
                for (var key in this.showCountryPickerList)
                {
                    if (this.showCountryPickerList.hasOwnProperty(key)) {
                        var country = this.showCountryPickerList[key];
                        if (country.hasOwnProperty('value') &&
                            country.hasOwnProperty('label') &&
                            country.label.match(/[A-Z]+/) !== null
                           ) {
                            newCountries.push(country);
                        }
                    }
                }
                this.showCountryPickerList = newCountries;
            }

            this.showPicker = (this.showZipCodePicker || this.showCountryPicker);
            this.customCountry = ko.observable(selectedCountry);
            this.customCountry.subscribe(function(value) {
                var shippingAddress = quoteModel.shippingAddress();
                if (shippingAddress.countryId != value) {
                    // console.log('Changed country to: ' + value);
                    shippingAddress.countryId = value;
                    quoteModel.shippingAddress(shippingAddress);
                    parent.searchPostCode();
                }
            });

            // console.log(this);

            // When rates changes, decoded agents and add-on if it exists
            if (typeof(this.shippingRates) === 'undefined') {
                // console.log('Initialized shipping-rates');
                this.shippingRates = ko.observableArray();
            } else {
                // console.log('Shipping rates existed -');
                // console.log(this.shippingRates());
                parent.refreshRates(this.shippingRates(), true);
            }
            this.shippingRates.subscribe(function (rates) {
                parent.refreshRates(rates);
            });
            return this;
        },
        refreshRates: function(rates, forceUpdate) {
            if (rates.length) {
                // console.log('New rates before process: ');
                // console.log(rates);
                // console.log(JSON.stringify(rates));

                var rateIndex, rate, decoded, updated;
                updated = false;
                for (rateIndex in rates) {
                    if (rates.hasOwnProperty(rateIndex)) {
                        rate = rates[rateIndex];
                        if (rate.hasOwnProperty('extension_attributes')) {

                            // Agents
                            if (rate.extension_attributes.hasOwnProperty('msunifaun_agents') &&
                                !rate.hasOwnProperty('msunifaun_agents')
                               ) {
                                try {
                                    decoded = $.parseJSON(rate.extension_attributes.msunifaun_agents);
                                } catch (e) {
                                    console.error("parseJSON error", e);
                                }
                                var agentId;
                                var reusingAgent = false;
                                if (typeof(decoded) === 'object' &&
                                    decoded.length) {
                                    updated = true;
                                    // console.log('Added agents field to "' + rate.carrier_title + '"');
                                    rate.msunifaun_agents = ko.observableArray(decoded);

                                    // Do we have a stored agent in window cache?
                                    if (typeof(window.msunifaun_selectedAgent) !== 'undefined' &&
                                        typeof(window.msunifaun_selectedAgent[rate.method_code]) !== 'undefined'
                                       ) {
                                        // console.log('Re-using agent stored in window:');
                                        // console.log(window.msunifaun_selectedAgent[rate.method_code]);
                                        for (agentId in decoded) {
                                            if (decoded.hasOwnProperty(agentId)) {
                                                if (decoded[agentId].id == window.msunifaun_selectedAgent[rate.method_code].id) {
                                                    rate.msunifaun_agent = ko.observable(rate.msunifaun_agents()[agentId]);
                                                    reusingAgent = true;
                                                    // console.log('Setting selected agent-id from window to ' + agentId);
                                                    // console.log('Rate after window agent:');
                                                    // console.log(rate);
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        // console.log('Not re-using agent stored in window:');
                                        if (typeof(window.msunifaun_selectedAgent) === 'undefined') {
                                            // console.log('window.msunifaun_selectedAgent is undefined');
                                        }
                                        if (typeof(window.msunifaun_selectedAgent) !== 'undefined' &&
                                            typeof(window.msunifaun_selectedAgent[rate.method_code]) === 'undefined') {
                                            // console.log('window.msunifaun_selectedAgent[' + rate.method_code + '] is undefined');
                                        }

                                        // Do we have a stored agent in server cache?
                                        if (typeof(rate.extension_attributes['msunifaun_agent']) !== 'undefined') {
                                            var decodedAgent = false;
                                            try {
                                                decodedAgent = $.parseJSON(rate.extension_attributes.msunifaun_agent);
                                            } catch (e) {
                                                console.error("parseJSON error", e);
                                            }
                                            if (typeof(decodedAgent) === 'object'
                                                && decodedAgent !== null
                                                && decodedAgent.hasOwnProperty('id')
                                               ) {
                                                for (agentId in decoded) {
                                                    if (decoded.hasOwnProperty(agentId)) {
                                                        if (decoded[agentId].hasOwnProperty('id')
                                                            && decoded[agentId].id === decodedAgent.id
                                                           ) {
                                                            rate.msunifaun_agent = ko.observable(rate.msunifaun_agents()[agentId]);
                                                            reusingAgent = true;
                                                            // console.log('Setting selected agent-id from server cache to ' + agentId);
                                                            // console.log('Rate after server cache agent:');
                                                            // console.log(rate);
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (!reusingAgent) {
                                        rate.msunifaun_agent = ko.observable();
                                    }
                                    rate.msunifaun_agent.subscribe(function(agent) {
                                        var rate = quoteModel.shippingMethod();
                                        // console.log('Mediastrategi Unifaun - agent selected for: "' + rate.carrier_title + '". Store in window..');
                                        parent.setAgent(rate, agent);
                                    });
                                }
                            }

                            var addonsHasSelectedProperty = true;
                            if (rate.hasOwnProperty('msunifaun_addons')) {
                                for (var addonKey in rate.msunifaun_addons) {
                                    if (rate.msunifaun_addons.hasOwnProperty(addonKey)) {
                                        if (typeof(rate.msunifaun_addons.selected) !== 'object') {
                                            addonsHasSelectedProperty = false;
                                            break;
                                        }
                                    }
                                }
                            }

                            // Add-ons
                            if (rate.extension_attributes.hasOwnProperty('msunifaun_addons')
                                && (!rate.hasOwnProperty('msunifaun_addons')
                                    || !addonsHasSelectedProperty)
                               ) {
                                try {
                                    decoded = $.parseJSON(rate.extension_attributes.msunifaun_addons);
                                } catch (e) {
                                    console.error('parseJSON error', e);
                                }
                                if (typeof(decoded) === 'object' &&
                                    decoded.length
                                   ) {
                                    // Set add-ons as not selected by default
                                    for (var key in decoded)
                                    {
                                        if (decoded.hasOwnProperty(key)) {
                                            var addon = decoded[key];
                                            var hasSelected = addon.hasOwnProperty("selected");
                                            var selected = (hasSelected ? addon.selected : false);
                                            if (typeof(window.msunifaun_selectedAddons) !== 'undefined' &&
                                                typeof(window.msunifaun_selectedAddons[rate.method_code]) !== 'undefined' &&
                                                typeof(window.msunifaun_selectedAddons[rate.method_code][addon.id]) !== 'undefined'
                                               ) {
                                                // console.log('Re-using add-on selected flag stored in window..');
                                                selected = window.msunifaun_selectedAddons[rate.method_code][addon.id];
                                            } else {
                                                // console.log('Not re-using add-on selected flag stored in window..');
                                                if (typeof(window.msunifaun_selectedAddons) === 'undefined') {
                                                    // console.log('window.msunifaun_selectedAddons is undefined');
                                                }
                                                if (typeof(window.msunifaun_selectedAddons) !== 'undefined' &&
                                                    typeof(window.msunifaun_selectedAddons[rate.method_code]) === 'undefined') {
                                                    // console.log('window.msunifaun_selectedAddons[' + rate.method_code + '] is undefined');
                                                }
                                                if (typeof(window.msunifaun_selectedAddons) !== 'undefined' &&
                                                    typeof(window.msunifaun_selectedAddons[rate.method_code]) !== 'undefined' &&
                                                    typeof(window.msunifaun_selectedAddons[rate.method_code][addon.id]) === 'undefined') {
                                                    // console.log('window.msunifaun_selectedAddons[' + rate.method_code + '][' + addon.id + '] is undefined');
                                                }
                                            }
                                            addon.selected = ko.observable(selected);
                                            eval(
                                                "addon.selected.subscribe(function(selected) {"
                                                    + "parent.setAddon(quoteModel.shippingMethod(), '" + addon.id + "', selected);"
                                                    + "});"
                                            );

                                            var newAddonFields = [];
                                            if (typeof(addon.fields) !== 'undefined' &&
                                                addon.fields.length
                                               ) {
                                                var addonField, newAddonField;
                                                for (var fieldKey in addon.fields)
                                                {
                                                    if (addon.fields.hasOwnProperty(fieldKey)) {
                                                        addonField = addon.fields[fieldKey];
                                                        if ((typeof(addonField.description) !== 'undefined' &&
                                                             addonField.description !== '') &&
                                                            (addonField.type === 'EMAIL' ||
                                                             addonField.type === 'PHONE' ||
                                                             addonField.type === 'TEXT')
                                                           ) {
                                                            var addonFieldStartValue = addonField.value;
                                                            if (typeof(window.msunifaun_addonFields) !== 'undefined' &&
                                                                typeof(window.msunifaun_addonFields[rate.method_code]) !== 'undefined' &&
                                                                typeof(window.msunifaun_addonFields[rate.method_code][addon.id]) !== 'undefined' &&
                                                                typeof(window.msunifaun_addonFields[rate.method_code][addon.id][addonField.id]) !== 'undefined'
                                                               ) {
                                                                // console.log('Using addon field value stored in window..');
                                                                // console.log(window.msunifaun_addonFields[rate.method_code][addon.id][addonField.id]);
                                                                addonFieldStartValue = window.msunifaun_addonFields[rate.method_code][addon.id][addonField.id];
                                                            } else {
                                                                // console.log('Not re-using addon field value stored in window..');
                                                                if (typeof(window.msunifaun_addonFields) === 'undefined') {
                                                                    // console.log('window.msunifaun_addonFields is undefined');
                                                                }
                                                                if (typeof(window.msunifaun_addonFields) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code]) === 'undefined') {
                                                                    // console.log('window.msunifaun_addonFields[' + rate.method_code + '] is undefined');
                                                                }
                                                                if (typeof(window.msunifaun_addonFields) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code]) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code][addon.id]) === 'undefined') {
                                                                    // console.log('window.msunifaun_addonFields[' + rate.method_code + '][' + addon.id + '] is undefined');
                                                                }
                                                                if (typeof(window.msunifaun_addonFields) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code]) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code][addon.id]) !== 'undefined' &&
                                                                    typeof(window.msunifaun_addonFields[rate.method_code][addon.id][addonField.id]) === 'undefined'
                                                                   ) {
                                                                    // console.log('window.msunifaun_addonFields[' + rate.method_code + '][' + addon.id + '][' + addonField.id + '] is undefined');
                                                                }
                                                            }

                                                            var addonFieldValue = ko.observable(addonFieldStartValue);

                                                            eval(
                                                                "addonFieldValue.subscribe(function(value) {"
                                                                    + "parent.setAddonField(quoteModel.shippingMethod(), '" + addon.id + "', '" + addonField.id + "', value);"
                                                                    + "});"
                                                            );

                                                            newAddonFields.push({
                                                                description: addonField.description,
                                                                id: addonField.id,
                                                                required: typeof(addonField.mandatory) !== 'undefined' && addonField.mandatory,
                                                                type: addonField.type,
                                                                value: addonFieldValue
                                                            });
                                                        }
                                                    }
                                                }
                                            }
                                            // console.log('Mediastrategi Unifaun - addon fields: ');
                                            // console.log(newAddonFields);
                                            addon.fields = newAddonFields;
                                        }
                                    }
                                    rate.msunifaun_addons = decoded;
                                    // console.log('Set addons to:');
                                    // console.log(rate.msunifaun_addons);
                                }
                            }

                        }
                    }
                }

                if (typeof(forceUpdate) === 'undefined') {
                    forceUpdate = false;
                }

                if (updated
                    || forceUpdate
                   ) {
                    // console.log('Updated something.. restarting parsing of rates..');
                    // console.log(rates);
                    shippingService.setShippingRates(rates);
                    // parent.rates(rates);
                    // parent.rates.splice(rateIndex, 1);
                    // parent.rates.push(rate);
                    // return;
                }

                if (!$('body').hasClass('msunifaun-entered-post-code')) {
                    $('body').addClass('msunifaun-entered-post-code');
                }
            } else {
                if ($('body').hasClass('msunifaun-entered-post-code')) {
                    $('body').removeClass('msunifaun-entered-post-code');
                }
            }
            // console.log('Mediastrategi Unifaun - new rates with pick-up locations and addons:');
            // console.log(rates);
        },
        saveRate: function(rate) {
            // console.log('Mediastrategi Unifaun - saveRate(rate)');
            // console.log(rate);
            // this.selectShippingMethod(rate);
            selectShippingMethodAction(rate);
            setShippingInformationAction();
            if ($('.checkout-klarna-index').length) {
                 // NOTE Only do this if Klarna is running
            } else {
                // quoteModel.shippingMethod(rate);
            }
        },
        searchKeyUp: function(d, e) {
            // Does user click return?
            if (e.keyCode == 13) {
                this.searchPostCode();
            }
        },
        searchPostCode: function() {
            var parent = this;
            var value = this.customPostCode();
            // console.log('Mediastrategi Unifaun - custom post code: ' + value);
            // console.log(parent);
            // console.log(quoteModel);

            var shippingAddress = quoteModel.shippingAddress();
            // console.log('Mediastrategi Unifaun - new shipping-address:');
            /* @see module-kco/view/frontend/web/js/model/kco.js */

            // Update shipping-address and trigger refresh
            var newShippingAddress = newCustomerAddress({
                city: shippingAddress.city,
                company: shippingAddress.company,
                countryId: shippingAddress.countryId,
                email: shippingAddress.email,
                firstname: shippingAddress.firstname,
                lastname: shippingAddress.lastname,
                postcode: value,
                prefix: shippingAddress.prefix,
                region: shippingAddress.region,
                regionId: shippingAddress.regionId,
                regionCode: shippingAddress.regionCode,
                street: shippingAddress.street,
                telephone: shippingAddress.telephone
            });
            // console.log(newShippingAddress);
            selectShippingAddress(newShippingAddress);
        },
        setAddon: function(rate, addonId, selected) {
            // console.log('Mediastrategi Unifaun - setAddon(rate, "' + addonId + '", "' + selected + '")');
            // console.log(rate);
            // console.log(addonId);
            // console.log(selected);
            if (typeof(window.msunifaun_selectedAddons) === 'undefined') {
                window.msunifaun_selectedAddons = {};
            }
            if (typeof(window.msunifaun_selectedAddons[rate.method_code]) === 'undefined') {
                window.msunifaun_selectedAddons[rate.method_code] = {};
            }
            if (window.msunifaun_selectedAddons[rate.method_code][addonId] !== selected) {
                window.msunifaun_selectedAddons[rate.method_code][addonId] = selected;
                // console.log('Addon selected flag change to:');
                // console.log(window.msunifaun_selectedAddons[rate.method_code][addonId]);
                this.saveRate(rate);
            }
        },
        setAddonField: function(rate, addonId, fieldId, value) {
            // console.log('Mediastrategi Unifaun - setAddonField(rate, "' + addonId + '", "' + fieldId + '", "' + value + '")');
            // console.log(rate);
            // console.log(addonId);
            // console.log(fieldId);
            // console.log(value);
            if (typeof(window.msunifaun_addonFields) === 'undefined') {
                window.msunifaun_addonFields = {};
            }
            if (typeof(window.msunifaun_addonFields[rate.method_code]) === 'undefined') {
                window.msunifaun_addonFields[rate.method_code] = {};
            }
            if (typeof(window.msunifaun_addonFields[rate.method_code][addonId]) === 'undefined') {
                window.msunifaun_addonFields[rate.method_code][addonId] = {};
            }
            if (window.msunifaun_addonFields[rate.method_code][addonId][fieldId] !== value) {
                window.msunifaun_addonFields[rate.method_code][addonId][fieldId] = value;
                // console.log('Addon field value changed to:');
                // console.log(window.msunifaun_addonFields[rate.method_code][addonId][fieldId]);
                this.saveRate(rate);
            }
        },
        setAgent: function(rate, agent) {
            // console.log('Mediastrategi Unifaun - setAgent(rate, agent)');
            // console.log(rate);
            // console.log(agent);
            if (typeof(window.msunifaun_selectedAgent) === 'undefined') {
                window.msunifaun_selectedAgent = {};
            }
            if (agent
                && (!window.msunifaun_selectedAgent[rate.method_code] ||
                    window.msunifaun_selectedAgent[rate.method_code].id !== agent.id)
               ) {
                window.msunifaun_selectedAgent[rate.method_code] = rate.msunifaun_agent();
                // console.log('Selected agent changed to:');
                // console.log(window.msunifaun_selectedAgent[rate.method_code]);
                this.saveRate(rate);
            }
        }
    };
    return function (target) {
        target = target.extend(mixin);

        if (typeof(target.defaults.template) !== 'undefined') {
            target.defaults.template =
                'Mediastrategi_Unifaun/cart/shipping-rates';
        }

        // console.log('Mediastrategi Unifaun - Extending cart shipping rates template');
        // console.log(target);
        return target;
    };
});
