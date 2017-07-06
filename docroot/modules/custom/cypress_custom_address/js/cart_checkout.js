/**
 * @file
 * Promocode behaviors.
 */

(function ($, Drupal) {
    Drupal.behaviors.netpce_cart = {
        attach: function (context, settings) {
         // Click on dummy checkout.
            $('#edit-checkout').hide();
            $('#checkout-dummy').click(function () {
                 $('#edit-checkout').trigger("click");
                return false;
            });

            $('.cart-form input[type="number"]').keydown(function (e) {
                var code = e.which;
                if (code == 13) {
                    e.preventDefault();
                    $('#edit-submit').trigger('click');
                }
            });
        }
    };

}(jQuery, Drupal));
