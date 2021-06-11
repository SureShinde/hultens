define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Klarna_Kco/js/model/klarna',
        'Magento_Checkout/js/action/get-totals'
    ],
    function (ko, $, Component, priceUtils, quote, klarna, getTotals) {
        "use strict";

        var canIncrement = ko.observable(true);

        return Component.extend({
            defaults: {
                template: 'Crealevant_Hultencheckout/summary/item/details'
            },

            canIncrement: canIncrement,

            customOptions: window.checkoutConfig.custom_options,

            getValue: function (quoteItem) {
                return quoteItem.name;
            },

            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },

            getItemThumbnail: function (itemId) {
                var options = this.customOptions;
                var thumbnail = '#';
                for (var key in options) {
                    if (itemId == key) {
                        thumbnail = options[key].image;
                    }
                    if ("items" in options[key]) {
                        var items = options[key].items;
                        for (var k in items) {
                            if (itemId == items[k].id) {
                                thumbnail = items[k].image;
                            }
                        }
                    }
                }
                return thumbnail;
            },

            getShippingInfo: function (itemId) {
                var options = this.customOptions;
                var shippingInfo = '';
                for (var key in options) {
                    if (itemId == key) {
                        if (options[key].red_xtra_status_text != null && options[key].red_xtra_status_text != "") {
                            shippingInfo = options[key].red_xtra_status_text;
                        } else {
                            shippingInfo = options[key].shipping_info;
                        }
                    }
                    if ("items" in options[key]) {
                        var items = options[key].items;
                        for (var k in items) {
                            if (itemId == items[k].id) {
                                shippingInfo = items[k].shipping_info;
                            }
                        }
                    }
                }
                return shippingInfo;
            },

            getItems: function (itemId) {
                var options = this.customOptions;
                for (var key in options) {
                    if (itemId == key) {
                        if ("items" in options[key]) {
                            return options[key].items;
                        }
                    }
                }
                return {};
            },

            incrementDown: function (item, event) {
                if (event.type !== 'click') {
                    return;
                }

                canIncrement(false);
                var cartId = quote.getQuoteId();
                var qty = (parseInt(item.qty) - 1);
                var data = {
                    itemUpdate: {
                        itemId: parseInt(item.item_id),
                        qty: qty,
                    }
                };
                if (typeof window._klarnaCheckout !== "undefined") {
                    window._klarnaCheckout(function (api) {
                        api.suspend();
                    });
                }

                $.ajax({
                    url: "/rest/default/V1/guest-carts/" + cartId + "/items/updateQty",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data)
                }).success(function (data) {
                    /*if (typeof window._klarnaCheckout !== "undefined") {
                        window._klarnaCheckout(function (api) {
                            api.resume();
                        });
                    }

                    quote.setTotals(data.totals);
                    if(parseInt(data.totals.items_qty) < 1) {
                        window.location.replace("/");
                    }
                    getTotals([]);
                    canIncrement(true);*/
                    location.reload();
                });
            },

            incrementUp: function (item, event) {
                if (event.type !== 'click') {
                    return;
                }

                canIncrement(false);
                var cartId = quote.getQuoteId();
                var qty = (parseInt(item.qty) + 1);
                var data = {
                    itemUpdate: {
                        itemId: parseInt(item.item_id),
                        qty: qty,
                    }
                };

                if (typeof window._klarnaCheckout !== "undefined") {
                    window._klarnaCheckout(function (api) {
                        api.suspend();
                    });
                }

                $.ajax({
                    url: "/rest/default/V1/guest-carts/" + cartId + "/items/updateQty",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data)
                }).success(function (data) {
                    /*if (typeof window._klarnaCheckout !== "undefined") {
                        window._klarnaCheckout(function (api) {
                            api.resume();
                        });
                    }

                    quote.setTotals(data.totals);
                    if(parseInt(data.totals.items_qty) < 1) {
                        window.location.replace("/");
                    }
                    getTotals([]);
                    canIncrement(true);*/
                    location.reload();
                });
            },

            bundleSelectionIncrementDown: function (item, event) {
                if (event.type !== 'click') {
                    return;
                }

                if (item.qty == 1) {
                    return;
                }

                canIncrement(false);
                var cartId = quote.getQuoteId();
                var qty = (parseInt(item.qty) - 1);
                var data = {
                    itemUpdate: {
                        itemId: parseInt(item.id),
                        qty: qty,
                    }
                };
                if (typeof window._klarnaCheckout !== "undefined") {
                    window._klarnaCheckout(function (api) {
                        api.suspend();
                    });
                }

                $.ajax({
                    url: "/rest/default/V1/guest-carts/" + cartId + "/items/updateQty",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data)
                }).success(function (data) {
                    location.reload();
                });
            },

            bundleSelectionIncrementUp: function (item, event) {
                if (event.type !== 'click') {
                    return;
                }

                canIncrement(false);
                var cartId = quote.getQuoteId();
                var qty = (parseInt(item.qty) + 1);
                var data = {
                    itemUpdate: {
                        itemId: parseInt(item.id),
                        qty: qty,
                    }
                };

                if (typeof window._klarnaCheckout !== "undefined") {
                    window._klarnaCheckout(function (api) {
                        api.suspend();
                    });
                }

                $.ajax({
                    url: "/rest/default/V1/guest-carts/" + cartId + "/items/updateQty",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data)
                }).success(function (data) {
                    location.reload();
                });
            },

            removeItem: function (itemId) {
                $.ajax({
                    url: window.checkoutConfig.url_remove_item,
                    data: {'item_id': itemId, 'form_key': $.cookie('form_key')},
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    showLoader: true,
                    timeout: 10000
                }).done(function (response) {
                    if (response.success) {
                        location.reload(true);
                    }
                });
            },

            checkIfDiscounted: function ($parent) {
                var csQuoteItemData = window.checkoutConfig.quoteItemData;
                var isSale = false;
                var currentdate = new Date();
                var current = currentdate.getFullYear() + "-"
                    + (currentdate.getMonth() + 1) + "-"
                    + currentdate.getDate() + " "
                    + currentdate.getHours() + ":"
                    + currentdate.getMinutes() + ":"
                    + currentdate.getSeconds();
                if (csQuoteItemData && csQuoteItemData.length) {
                    window.checkoutConfig.quoteItemData.forEach(function (element) {
                        if (element.item_id == $parent.item_id) {
                            if (typeof element.product.special_price != "undefined" && element.product.special_price) {
                                var start = element.product.special_from_date;
                                var end = element.product.special_to_date;
                                if ((!start && !end) ||
                                    (new Date(current) >= new Date(start) && !end) ||
                                    (new Date(current) <= new Date(end) && !start) ||
                                    (new Date(current) >= new Date(start) && new Date(current) <= new Date(end))) {
                                    isSale = true;
                                }
                            }
                        }
                    });
                }
                return isSale;
            },

            getOriginalPrice: function ($parent) {
                var csQuoteItemData = window.checkoutConfig.quoteItemData;
                var basePrice = 0;
                if (csQuoteItemData && csQuoteItemData.length) {
                    window.checkoutConfig.quoteItemData.forEach(function (element) {
                        if (element.item_id == $parent.item_id) {
                            if (typeof element.product.price != "undefined" && element.product.price) {
                                basePrice = element.product.price * $parent.qty;
                            }
                        }
                    });
                }
                return parseFloat(basePrice);
            },
        });
    }
);
