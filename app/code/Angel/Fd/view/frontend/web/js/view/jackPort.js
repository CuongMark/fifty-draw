/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Angel_Fd/js/model/raffle',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($, ko, Component, fd, priceUtils, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Angel_Fd/jack-port'
        },
        priceFormat : window.checkoutConfig?window.checkoutConfig.priceFormat:{"pattern":"$%s","precision":2,"requiredPrecision":2,"decimalSymbol":".","groupSymbol":",","groupLength":3,"integerRequired":false},
        jackPot : fd.jackPot,

        /** @inheritdoc */
        initialize: function () {
            var self = this;
            this._super();
            this.jackPotFormated = ko.computed(function(){
                var jackPot = priceUtils.formatPrice(self.jackPot(), self.priceFormat);
                return $t('Jack Pot: ') + jackPot;
            });
        },
    });
});
