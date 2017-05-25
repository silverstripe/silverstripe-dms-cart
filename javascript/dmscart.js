(function ($) {
  "use strict";

  $.entwine('ss', function ($) {
    $('input.dms-allowed-in-cart').entwine({
      /**
       * Toggle the "maximum cart quantity" field visibility depending on whether "allowed in document cart" is checked
       */
      onclick: function (e) {
        jQuery('.field.dms-maximum-cart-quantity').toggle();
        this.getElements().removeClass('hide');
      },
      /**
       * Initially show the "maximum cart quantity" field visibility if the "allowed in document cart" checkbox
       * is checked
       */
      onmatch: function(e) {
        if (this.is(':checked')) {
          this.getElements().removeClass('hide');
        }
      },
      /**
       * Returns all DOM elements with the field's class applied
       */
      getElements: function() {
        return jQuery('.dms-maximum-cart-quantity');
      }
    });
  });
}(jQuery));
