/**
 * @file
 * Shippping behaviors.
 */

(function ($, Drupal, drupalSettings) {

    'use strict';
    // Change field type text into password on checkout page.
    if ($('#edit-payment-information-add-payment-method-payment-details-security-code').length) {
        $(document).on('click', '#edit-payment-information-add-payment-method-payment-details-security-code', function () {
             $(this).attr('type', 'password');
        });
    }
    var order_total = 0.00;
    var on_load_executed = false;
    var changeShippingRate = function () {
        var shipping_method_rate = 0.00,
          shipping_method = '';

        $('body.path-checkout .field--widget-commerce-shipping-rate' +
          ' input[type=radio]:checked').each(function (index) {
            shipping_method = $(this).parent().text();
            shipping_method_rate += parseFloat(shipping_method.split('$')[1]);
        });
        var order_total_line_shipping = '' +
          '<div class="order-total-line order-total-line__shipping">' +
          '<span class="order-total-line-label">Shipping </span>' +
          '<span class="order-total-line-value">$' + shipping_method_rate.toFixed(2) + '</span>' +
          '</div>';
        if ($('.order-total-line__shipping').length) {
            $('.order-total-line__shipping .order-total-line-value').text('$' + shipping_method_rate.toFixed(2));
        }
        else {
            $(order_total_line_shipping).insertBefore('body.path-checkout .order-total-line__total');
        }
        var new_order_total = order_total + shipping_method_rate;
        // Var new_total = new_order_total.toFixed(2);.
        // Adding custom comma in order total.
        $('.order-total-line__total .order-total-line-value').text('$' + new_order_total.toLocaleString('en-US', {minimumFractionDigits: 2}));
        if (new_order_total == 0) {
            $('#edit-actions-next').html('Complete Purchase');
        }
        else {
            $('#edit-actions-next').html('Pay and Complete Purchase');
        }
    };

    /**
     * Behaviors for shipping charge in the Cypress Shipment method pane form.
     *
     * @type {Drupal~behavior}
     *
     * @prop {Drupal~behaviorAttach} attach
     *   Attaches summary behavior for shipment methods
     *   in the Cypress Shipment method pane form.
     */
    Drupal.behaviors.cypressShippingRate = {
        attach: function (context) {

            // Change the title of Shipping address on order information page.
            $('.path-checkout .field--name-field-contact-address .panel-default .panel-heading .panel-title').html('Billing Address');

            if (!on_load_executed) {
                order_total = $('.order-total-line__total .order-total-line-value', context).text();
                // Replace the comma in order total.
                order_total = order_total.replace(/,/g , "");
                order_total = parseFloat(order_total.split('$')[1]);
                // sub_total = $('.order-total-line__subtotal .order-total-line-value').text();
                // sub_total = parseFloat(sub_total.split('$')[1]);
                // Change shipping rate on page load.
                changeShippingRate();
                on_load_executed = true;
            }
            // Change shipping rate when user changes the shipping method.
            $('body.path-checkout .field--widget-commerce-shipping-rate' +
              ' input[type=radio]').on('change', function () {
                  changeShippingRate();
            });

          $('#edit-cypress-review', context).addClass('shipping-address');
          $('#edit-coupon-redemption', context).wrapAll("<div class='coupon-redemption'></div>");
          $('.shipping-address,.coupon-redemption', context).wrapAll("<div class='order-information-row1'></div>");
          $('#payment-information-wrapper', context).addClass('payment-information');
          $('#parts-information-wrapper', context).addClass('parts-information');
          $('.payment-information,.parts-information', context).wrapAll("<div class='order-information-row2'></div>");
          $('.order-information-row1,.order-information-row2', context).addClass('col-sm-12');
          $('.shipping-address,.coupon-redemption,.payment-information,.parts-information', context).addClass('col-sm-6');
        }
    };

})(jQuery, Drupal, drupalSettings);
