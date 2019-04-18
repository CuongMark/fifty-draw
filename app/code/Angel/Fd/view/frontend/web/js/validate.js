/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'ko',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'Angel_Fd/js/action/purchase-tickets',
    'Angel_Fd/js/model/raffle',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm',
    'mage/validation'
], function ($, ko, mage, validation, purchaseAction, raffle, customerData, confirmation) {
    'use strict';

    $.widget('fd.fdValidate', {
        isLoading: ko.observable(false),
        tickets: raffle.tickets,
        jackPot: raffle.jackPot,
        isAddToCart: true,
        options: {
            bindSubmit: false,
            radioCheckboxClosest: '.nested'
        },
        isLoggedIn : function () {
            var customer = customerData.get('customer');
            return customer && customer().firstname;
        },

        submitPurchaseRequest : function (form) {
            var self = this;
            var formElement = $('#'+form.id),
                formDataArray = formElement.serializeArray();
            var purchaseData = {};
            formDataArray.forEach(function (entry) {
                purchaseData[entry.name] = entry.value;
            });

            if (formElement.validation() &&
                formElement.validation('isValid')
            ) {
                self.isLoading(true);
                $('#product-addtocart-button').addClass('disabled');
                $('#ticket-purchase-button').addClass('disabled');
                purchaseAction(purchaseData);
            }
        },

        /**
         * Uses Magento's validation widget for the form object.
         * @private
         */
        _create: function () {
            var self = this;
            this.jackPot(parseFloat(self.options.jackPot));

            this.element.validation({
                radioCheckboxClosest: this.options.radioCheckboxClosest,

                /**
                 * Uses catalogAddToCart widget as submit handler.
                 * @param {Object} form
                 * @returns {Boolean}
                 */
                submitHandler: function (form, event) {
                    if (!self.isLoggedIn()){
                        window.location.href = self.options.loginUrl;
                        return false;
                    }
                    if (self.isLoading()){
                        return false;
                    }

                    if (self.isAddToCart) {
                        var jqForm = $(form).catalogAddToCart({
                            bindSubmit: bindSubmit
                        });

                        jqForm.catalogAddToCart('submitForm', jqForm);
                    } else {
                        confirmation({
                            title: 'Accept Purchase',
                            content: 'Are you sure you want to purchase ' + $('#qty').val() + ' ticket(s).',
                            actions: {
                                confirm: function () {
                                    self.submitPurchaseRequest(form);
                                    return false;
                                },
                                cancel: function () {
                                    return false;
                                }
                            }
                        });
                    }
                    return false;
                }
            });
            $('#ticket-purchase-button').click(function () {
                self.isAddToCart = false;
            });
            $('#product-addtocart-button').click(function () {
                self.isAddToCart = true;
            });
            
            purchaseAction.registerPurchaseCallback(function (purchaseData, response) {
                if (response && response.ticket_id){
                    var tickets = self.tickets();
                    tickets.push(response);
                    self.tickets(tickets);
                    self.jackPot(self.jackPot() + parseFloat(response.price));
                }
                self.isLoading(false);
                $('#product-addtocart-button').removeClass('disabled');
                $('#ticket-purchase-button').removeClass('disabled');
            });
            $('#product-addtocart-button').removeClass('disabled');
            $('#ticket-purchase-button').removeClass('disabled');
        }
    });

    return $.fd.fdValidate;
});
