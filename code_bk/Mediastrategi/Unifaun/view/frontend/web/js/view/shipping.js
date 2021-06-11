/** @see Magento_Checkout/js/view/shipping */
/* global define */
define([
    'jquery',
    'ko',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/new-customer-address',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/set-shipping-information'
], function (
    $,
    ko,
    wrapper,
    quoteModel,
    selectShippingAddress,
    newCustomerAddress,
    shippingService,
    setShippingInformationAction
) {
    'use strict';

    var mixin = {
        initialize: function()
        {
            this._super();
            // console.log('Mediastrategi Unifaun - shipping initialize()');

            var parent = this;

            this.customPostCode = ko.observable('');
            this.showZipCodePicker = (typeof(window.msunifaun_showZipCodePicker) !== 'undefined'
                                      && window.msunifaun_showZipCodePicker);
            this.showCountryPicker = (typeof(window.msunifaun_showCountryPicker) !== 'undefined'
                                      && window.msunifaun_showCountryPicker);
            this.showCountryPickerList = (typeof(window.msunifaun_showCountryPickerList) !== 'undefined'
                                          ? window.msunifaun_showCountryPickerList
                                          : false);
            this.selectedMethod = ko.observable('');
            if (typeof(this.isSelected) !== 'undefined') {
                // console.log('Mediastrategi Unifaun - isSelected is defined: ' + this.isSelected());
                this.selectedMethod(this.isSelected());
            }

            var selectedCountry = false;
            var shippingAddress = quoteModel.shippingAddress();
            if (shippingAddress.countryId) {
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
            if (typeof(this.rates) === 'undefined') {
                this.rates = ko.observableArray();
            }
            this.rates.subscribe(function (rates) {
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
                                            if (typeof(rate.extension_attributes.hasOwnProperty('msunifaun_agent') !== 'undefined')) {
                                                var decodedAgent = false;
                                                try {
                                                    decodedAgent = $.parseJSON(rate.extension_attributes.msunifaun_agent);
                                                } catch (e) {
                                                    console.error("parseJSON error", e);
                                                }
                                                if (typeof(decodedAgent) === 'object' &&
                                                    decodedAgent) {
                                                    for (agentId in decoded) {
                                                        if (decoded.hasOwnProperty(agentId) &&
                                                            typeof(decoded[agentId].id) !== 'undefined' &&
                                                            typeof(decodedAgent.id) !== 'undefined'
                                                           ) {
                                                            if (decoded[agentId].id === decodedAgent.id) {
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

                            }
                        }
                    }
                    if (updated) {
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
            });

            this.selectedMethod.subscribe(function(selectedRate) {
                // console.log('Mediastrategi Unifaun - selected method chaged to: ' + selectedRate);
                if (typeof(parent) !== 'undefined'
                    && typeof(parent.rates) !== 'undefined'
                   ) {
                    // console.log('rates:');
                    // console.log(parent.rates());
                    var rates = parent.rates();
                    for (var rateIndex in rates)
                    {
                        if (rates.hasOwnProperty(rateIndex)) {
                            var rate = rates[rateIndex];
                            if (typeof(rate.carrier_code) !== 'undefined'
                                && typeof(rate.method_code) !== 'undefined'
                                && rate.carrier_code + '_' + rate.method_code == selectedRate
                               ) {
                                // console.log('Mediastrategi Unifaun - found selected rate');
                                // console.log(rate);
                                parent.saveRate(rate);
                                break;
                            }
                        }
                    }
                }
            });

            // When selected shipping method changes externally, select in internally as well
            quoteModel.shippingMethod.subscribe(function(method) {
                var methodId = method['carrier_code'] + '_' + method['method_code'];
                if (parent.selectedMethod() !== methodId) {
                    parent.selectedMethod(method['carrier_code'] + '_' + method['method_code']);
                }
            });

            return this;
        },
        saveRate: function(rate) {
            // console.log('Mediastrategi Unifaun - saveRate(rate)');
            // console.log(rate);
            this.selectShippingMethod(rate);
            if ($('.checkout-klarna-index').length) {
                setShippingInformationAction(); // NOTE Only do this if Klarna is running
            }
            // quoteModel.shippingMethod(rate);
        },
        searchKeyUp: function(d, e) {
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

        // NOTE: Support for different Magento 2 versions here
        if (typeof(target.defaults.shippingMethodListTemplate) !== 'undefined') {
            target.defaults.shippingMethodListTemplate =
                'Mediastrategi_Unifaun/shipping-address/shipping-method-list';
            target.defaults.shippingMethodItemTemplate =
                'Mediastrategi_Unifaun/shipping-address/shipping-method-item';
        } else if (typeof(target.defaults.template) !== 'undefined') {
            target.defaults.template =
                'Mediastrategi_Unifaun/shipping';
        }

        // console.log('Mediastrategi Unifaun - extended shipping.js:');
        // console.log(target);
        return target;
    };
});
