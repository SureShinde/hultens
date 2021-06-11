/* global define */
define(
    [],
    function () {
        'use strict';
        return {
            getRules: function () {
                return {
                    'city': {
                        'required': true
                    },
                    'country_id': {
                        'required': true
                    },
                    'postcode': {
                        'required': true
                    },
                    'street': {
                        required: true
                    }
                };
            }
        };
    }
);
