(function ($) {
  "use strict";

  $(window).load(() => {
    // Check if checkbox is checked
    // const wasChecked = localStorage.getItem(
    //   "clmte-compensation-checkbox-checked"
    // );

    // Open panel if info is clicked
    $("#clmte-info").on("click", (e) => {
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

      // $("#clmte-checkbox").load(location.href + " #clmte-checkbox");
    };

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

    // If checkbox is clicked
    $("#clmte-checkbox").on("click", (e) => {
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
            // Update cart
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
            // Update cart
            updateCart();
          },
        });
      }
    });
  });
})(jQuery);
