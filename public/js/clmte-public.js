(function ($) {
  "use strict";

  $(window).load(() => {
    // Check if checkbox is checked
    const wasChecked = localStorage.getItem(
      "clmte-compensation-checkbox-checked"
    );

    if (wasChecked == "true") {
      // Check it
      $("#clmte-compensate").prop("checked", true);
    } else {
      // Uncheck it
      $("#clmte-compensate").prop("checked", false);
    }

    // If checkbox is clicked
    $("#clmte-compensate").click(() => {
      const isChecked = $("#clmte-compensate").is(":checked");
      localStorage.setItem("clmte-compensation-checkbox-checked", isChecked);

      if (isChecked) {
        // Add Compensation
        jQuery.ajax({
          method: "post",
          url: ajax_object.ajax_url,
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
          url: ajax_object.ajax_url,
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
