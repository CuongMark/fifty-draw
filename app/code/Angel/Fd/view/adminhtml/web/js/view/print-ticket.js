/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Angel_Fd/js/model/fifty-draw'
], function ($, ko, Component, fd) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Angel_Fd/print-ticket'
        },
        printTickets : fd.printTickets,

        /** @inheritdoc */
        initialize: function () {
            var self = this;
            this._super();
        },
    });
});
