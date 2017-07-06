/**
 * @file
 */

(function ($) {
    Drupal.behaviors.cancellation = {
        attach: function (context, settings) {
             $('[id*=edit-fullorder-checkbox-]').click(function () {
                 // All checkboxes to be unchecked on click of fullsession checkbox.
                /* $('input:checkbox').not(this).prop('checked', false);*/
                 $("[id*=edit-singleitem-checkbox-]").prop('checked', this.checked);
             });
             // Full session checkbox to be unchecked on any change event of single session checkbox.
             $("[id*=edit-singleitem-checkbox-]").change(function () {
                 if ($('[id*=edit-singleitem-checkbox-]:checked').length === $('[id*=edit-singleitem-checkbox-]').length) {
                     $('[id*=edit-fullorder-checkbox-]').prop('checked', true);
                 }
                 else {
                     $('[id*=edit-fullorder-checkbox-]').prop('checked', false);
                 }

             });
        }
    }
}(jQuery));
