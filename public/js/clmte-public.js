(function ($) {
  "use strict";

  $(window).load(() => {
    // Open panel if info is clicked
    $(".woocommerce").on("click", "#clmte-info", (e) => {
      const panel = document.getElementById("clmte-panel");
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    });

    const updateBtn = $('button[name="update_cart"]');

    const updateCart = () => {
      // location.reload();
      $("[name='update_cart']").prop("disabled", false);
      $("[name='update_cart']").trigger("click");
    };

    const updateCheckbox = () => {
      let checked = false;

      // Check if compensation product is in cart
      const elements = $("a.remove");
      elements.each(function () {
        const product_id = $(this).attr("data-product_id");
        if (product_id == clmte.compensation_product_id) {
          checked = true;
        }
      });

      // If so, check the checkbox
      if (checked) {
        // Check it
        $("#clmte-checkbox").prop("checked", true);
      } else {
        // Uncheck it
        $("#clmte-checkbox").prop("checked", false);
      }
    };

    updateCheckbox();

    $("body").on("updated_cart_totals", function () {
      updateCheckbox();
    });

    // If checkbox is clicked
    $(".woocommerce").on("click", "#clmte-checkbox", (e) => {
      const isChecked = $("#clmte-checkbox").is(":checked");
      // localStorage.setItem("clmte-compensation-checkbox-checked", isChecked);

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
  });
})(jQuery);
