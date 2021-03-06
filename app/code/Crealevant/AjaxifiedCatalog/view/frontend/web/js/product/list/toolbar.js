define([
    "jquery",
    "jquery/ui",
    "Magento_Theme/js/view/messages",
    "ko",
    "Magento_Catalog/js/product/list/toolbar",
    "Crealevant_AjaxifiedCatalog/js/jquery.history",
    "domReady!",
], function($, ui, messageComponent, ko) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */

    $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {
        options: {
            modeControl: '[data-role="mode-switcher"]',
            directionControl: '[data-role="direction-switcher"]',
            orderControl: '[data-role="sorter"]',
            limitControl: '[data-role="limiter"]',
            pagerControl: '[data-role="pager"], .pages-items a',
            mode: 'product_list_mode',
            direction: 'product_list_dir',
            order: 'product_list_order',
            limit: 'product_list_limit',
            pager: 'p',
            modeDefault: 'grid',
            directionDefault: 'asc',
            orderDefault: 'position',
            limitDefault: '9',
            pagerDefault: '1',
            productsToolbarControl: '.toolbar.toolbar-products',
            productsListBlock: '.products.wrapper',
            layeredNavigationFilterBlock: '.block.filter.layered-wrapper',
            filterItemControl: '.block.filter.layered-wrapper .item a, .block.filter .item a.action.remove, .block.filter .filter-clear,.block.filter .swatch-option-link-layered, .pages-items a',
            url: '',
            history: window.History
        },

        _create: function() {
            this._super();
            this._bind($(this.options.pagerControl), this.options.pager, this.options.pagerDefault);
            $(this.options.filterItemControl)
                .off('click.' + this.namespace + 'productListToolbarForm')
                .on('click.' + this.namespace + 'productListToolbarForm', {}, $.proxy(this.applyFilterToProductsList, this));
            var History = window.History;
            //var self = this;
            //detecting back/forward is clicked
            window.addEventListener('popstate', function(e) {
                var State = History.getState();
                if (State.data.path != null) {
                    //var urlParts = State.data.path.split('?');
                    History.log(State);
                    window.location.href = State.data.path;
                    //self.updateUrl(urlParts[0], urlParts[1]);

                    //self.makeAjaxCall(urlParts[0], urlParts[1]);

                } else {
                    window.location.href = window.location.href.split('#')[0];
                }
            });
        },

        _bind: function(element, paramName, defaultValue) {
            /**
             * Prevent double binding of these events because this component is being applied twice in the UI
             */

            if (element.is("select")) {
                element
                    .off('change.' + this.namespace + 'productListToolbarForm')
                    .on('change.' + this.namespace + 'productListToolbarForm', { paramName: paramName, default: defaultValue }, $.proxy(this._processSelect, this));
            } else {
                element
                    .off('click.' + this.namespace + 'productListToolbarForm')
                    .on('click.' + this.namespace + 'productListToolbarForm', { paramName: paramName, default: defaultValue }, $.proxy(this._processLink, this));
            }
        },

        applyFilterToProductsList: function(evt) {
            evt.preventDefault();
            var link = $(evt.currentTarget);
            var urlParts = link.attr('href').split('?');
            this.updateUrl(urlParts[0], urlParts[1]);
            this.makeAjaxCall(urlParts[0], urlParts[1]);
        },

        updateUrl: function(url, paramData) {
            if (!url) {
                return;
            }
            if (paramData && paramData.length > 0) {
                url += '?' + paramData;
            }
            this.options.history.pushState({ path: url }, null, url);
            this.options.history.replaceState({ path: url }, null, url);
        },

        getParams: function(urlParams, paramName, paramValue, defaultValue) {
            var paramData = {},
                parameters;

            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                if (parameters[1] !== undefined) {
                    paramData[parameters[0]] = parameters[1];
                } else {
                    paramData[parameters[0]] = '';
                }
            }

            paramData[paramName] = paramValue;
            if (paramValue == defaultValue) {
                delete paramData[paramName];
            }
            return window.decodeURIComponent($.param(paramData).replace(/\+/g, '%20'));
        },

        _updateContent: function(content) {
            $(this.options.productsToolbarControl).remove();
            if (content.products_list) {
                $(this.options.productsListBlock)
                    .replaceWith(content.products_list);
            }

            if (content.filters) {
                $(this.options.layeredNavigationFilterBlock).replaceWith(content.filters)
            }

            $('body').trigger('contentUpdated');
        },

        updateContent: function(content) {
            this._updateContent(content)
        },


        changeUrl: function(paramName, paramValue, defaultValue) {
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = this.getParams(urlParams, paramName, paramValue, defaultValue);
            this.updateUrl(baseUrl, paramData);
            this.makeAjaxCall(baseUrl, paramData);
        },

        makeAjaxCall: function(baseUrl, paramData) {
            var self = this;
            $.ajax({
                url: baseUrl,
                data: (paramData && paramData.length > 0) ? paramData + '&ajax=1' : 'ajax=1',
                type: 'get',
                dataType: 'json',
                cache: true,
                showLoader: true
            }).done(function(response) {
                if (response.success) {
                    self.updateContent(response.html);
                } else {
                    var msg = response.error_message;
                    if (msg) {
                        self.setMessage({
                            type: 'error',
                            text: msg
                        });
                    }
                }
            }).fail(function(error) {
                self.setMessage({
                    type: 'error',
                    text: 'Sorry, something went wrong. Please try again later.'
                });
            });
        },

        setMessage: function(obj) {
            var messages = ko.observableArray([obj]);
            messageComponent().messages({
                messages: messages
            });
        }
    });

    return $.mage.productListToolbarForm;
});
