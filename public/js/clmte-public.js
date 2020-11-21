(function ($) {
  "use strict";

  $(window).load(() => {
    // Check if checkbox is checked
    // const wasChecked = localStorage.getItem(
    //   "clmte-compensation-checkbox-checked"
    // );

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
      $("#clmte-compensate").prop("checked", true);
    } else {
      // Uncheck it
      $("#clmte-compensate").prop("checked", false);
    }

    // If checkbox is clicked
    $("#clmte-compensate").click(() => {
      const isChecked = $("#clmte-compensate").is(":checked");
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
            location.reload();
          },
        });
      } else {
        console.log("unchecked!");
        // Remove compensation
        jQuery.ajax({
          method: "post",
          url: clmte.ajax_url,
          data: {
            action: "remove_compensation_from_cart",
          },
          complete: () => {
            // reload page
            location.reload();
          },
        });
      }
    });
  });
})(jQuery);
