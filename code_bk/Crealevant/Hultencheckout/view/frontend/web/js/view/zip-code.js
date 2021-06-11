/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(['uiComponent'], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Crealevant_Hultencheckout/zipcode'
        },
        visible: true,

        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this;
            this._super();
        }
    });
});
