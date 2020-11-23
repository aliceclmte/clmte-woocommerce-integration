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

  // Update the checkbox depending on if a CLMTE compensation is in cart
  const updateCheckbox = () => {
    let checked = false;

    // Check if compensation products is in cart
    const elements = $("a.remove");

    // Check each item
    elements.each(function () {
      const product_id = $(this).attr("data-product_id");
      if (product_id == clmte.compensation_product_id) {
        checked = true;
      }
    });

    if (checked) {
      $("#clmte-checkbox").prop("checked", true); // check it
    } else {
      // Uncheck it
      $("#clmte-checkbox").prop("checked", false); // uncheck it
    }
  };

  // ON DOCUMENT LOAD
  $(window).load(() => {
    // Update status as soon as cart loads
    updateCheckbox();

    ///////////////////////////////////
    // EVENT LISTENERS
    ///////////////////////////////////

    // Check for a cart update
    $("body").on("updated_cart_totals", function () {
      updateCheckbox();
    });

    // Check for a click on checkbox
    $(".woocommerce").on("click", "#clmte-checkbox", (e) => {
      const isChecked = $("#clmte-checkbox").is(":checked");

      if (isChecked) {
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
      } else {
        // Remove compensation
        jQuery.ajax({
          method: "post",
          url: clmte.ajax_url,
          data: {
            action: "remove_compensation_from_cart",
          },
          complete: () => {
            updateCart();
          },
        });
      }
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
