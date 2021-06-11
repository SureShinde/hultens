var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Mediastrategi_Unifaun/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/cart/shipping-rates': {
                'Mediastrategi_Unifaun/js/view/cart/shipping-rates': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Mediastrategi_Unifaun/js/view/shipping': true
            },
            'Klarna_Kco/js/view/shipping-method': {
                'Mediastrategi_Unifaun/js/view/klarna-kco/shipping-method': true
            }
        }
    }
};
