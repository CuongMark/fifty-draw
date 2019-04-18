/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    return {
        name: ko.observable(''),
        jackPot: ko.observable(0),
        tickets: ko.observable([]),
        printTickets: ko.observable([]),
        setTicketsToPrint: function (tickets) {
            var ticketPrints = [];
            tickets.forEach(function (el, index) {
               var start = parseInt(el.start);
               var end = parseInt(el.end);
               for (var i = start; i<= end; i++){
                   var email = el.customer_email;
                   var sku = el.product_sku;
                   ticketPrints.push({
                       ticket_number: i,
                       email: email,
                       sku: sku
                   });
                }
            });
            this.printTickets(ticketPrints);
        }
    };
});
