/* global define */
define([], function () {
    'use strict';

    return function(target) {
        // console.log("Mediastrategi UODC - extending object");
        // Use a custom template for Klarna Shipping method
        target.defaults.template = "Mediastrategi_Unifaun/klarna-kco/shipping-method";
        // console.log(target);
        return target;
    };

});
