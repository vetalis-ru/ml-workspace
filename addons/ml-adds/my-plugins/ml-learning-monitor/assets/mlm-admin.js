jQuery(function ($) {
  function initEmailTabs() {
    const $tabs = $("#mlm_email_tabs");
    if ($tabs.length && !$tabs.hasClass("ui-tabs")) {
      $tabs.tabs();
    }
  }

  function updateVisibility() {
    const enabled = $("#mlm_enabled").is(":checked");
    if (enabled) {
      $("#mlm_monitor_block").slideDown(150, initEmailTabs);
    } else {
      $("#mlm_monitor_block").slideUp(150);
      $("#mlm_sleepers_container").empty();
      $("#mlm_sleepers_status").text("");
    }
  }

  // init
  initEmailTabs();
  updateVisibility();

  // toggle block
  $("#mlm_enabled").on("change", updateVisibility);

  // load sleepers (button)
  function loadSleepers(termId, page) {
    $("#mlm_sleepers_status").text("Загрузка...");
    $("#mlm_sleepers_container").empty();

    $.post(MLM.ajaxUrl, {
      action: "mlm_get_sleepers",
      nonce: MLM.nonce,
      term_id: termId,
      page: page,
    })
      .done(function (resp) {
        if (!resp || !resp.success) {
          $("#mlm_sleepers_status").text("Ошибка загрузки");
          return;
        }
        $("#mlm_sleepers_status").text("");
        $("#mlm_sleepers_container").html(resp.data.html);
      })
      .fail(function () {
        $("#mlm_sleepers_status").text("Ошибка загрузки");
      });
  }

  $("#mlm_show_sleepers").on("click", function () {
    const termId = parseInt($(this).data("term-id"), 10) || 0;
    if (!termId) return;
    loadSleepers(termId, 1);
  });

  // pagination arrows
  $(document).on("click", ".mlm-page-btn", function () {
    if ($(this).is(":disabled")) return;

    const termId = parseInt($(this).data("term-id"), 10) || 0;
    const page = parseInt($(this).data("page"), 10) || 1;
    if (!termId || page < 1) return;

    loadSleepers(termId, page);
  });
});
