/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Angel_Fd/js/model/raffle'
], function ($, ko, Component, tickets) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Angel_Fd/tickets'
        },
        tickets : tickets.tickets,
        hasTickets : ko.observable(false),

        /** @inheritdoc */
        initialize: function () {
            var self = this;
            self.tickets.subscribe(function () {
                self.hasTickets('selected_'+self.tickets());
            });
            this._super();
        },
    });
});
