(function ($) {
  "use strict";

  ///////////////////////////////////
  // CUSTOM FUNCTIONS
  ///////////////////////////////////

  // Update cart by simulating a press on the "Update Cart" button
  const updateCart = () => {
    $("[name='update_cart']").prop("disabled", false);
    $("[name='update_cart']").trigger("click");
  };

  // ON DOCUMENT LOAD
  $(window).load(() => {
    ///////////////////////////////////
    // EVENT LISTENERS
    ///////////////////////////////////

    // Check for a click on checkbox
    $(".woocommerce").on("click", "#clmte-compensate", (e) => {
      e.preventDefault();

      // Add Compensation
      jQuery.ajax({
        method: "post",
        url: clmte.ajax_url,
        data: {
          action: "add_compensation_to_cart",
        },
        complete: () => {
          updateCart();
        },
      });
    });

    // Open panel if info icon is clicked
    $(".woocommerce").on("click", "#clmte-info", (e) => {
      // Get panel element
      const panel = document.getElementById("clmte-panel");

      // Animate the panel section
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    });
  });
})(jQuery);
