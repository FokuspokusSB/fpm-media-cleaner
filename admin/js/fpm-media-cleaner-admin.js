(function ($) {
  "use strict";
  function log(msg, type = "log") {
    console[type](`----------------------------------`);
    console[type](`[FPM Media Cleaner]`);
    console[type](`${msg}`);
    console[type](`----------------------------------`);
  }
  /**
   *
   * @param {'media-clean-start' | '...'} action
   * @param {*} data
   * @returns
   */
  function request(action, data) {
    return $.post(ajaxurl, { action, ...data }).pipe(function (response) {
      try {
        return JSON.parse(response);
      } catch (e) {
        log(e.message, "error");
        return response;
      }
    });
  }

  function getTemplateElements() {
    return {
      status: document.querySelector("[data-options-status]"),
      lastUpdate: document.querySelector("[data-options-last-update]"),
      count: document.querySelector("[data-options-count]"),
      progress: document.querySelector("[data-fpm-media-progress]"),
      refreshBtn: document.querySelector("[data-fpm-media-cleaner-refresh]"),
      purgeBtn: document.querySelector("[data-fpm-media-cleaner-remove]"),
      activeCount: document.querySelector("[data-fpm-media-cleaner-count]"),
    };
  }

  function createDataTableTd(value) {
    const td = document.createElement("td");
    td.innerHTML = value;
    return td;
  }

  function getCount() {
    const elements = getTemplateElements();
    request("media-clean-get-count", {})
      .done(function (response) {
        elements.activeCount.innerHTML = response;
        const allCounts = Number.parseInt(elements.count.innerHTML);
        const count = Number.parseInt(response);

        if (!Number.isNaN(allCounts) && !Number.isNaN(count)) {
          elements.progress.setAttribute("max", allCounts);
          elements.progress.setAttribute("value", allCounts - count);
        } else {
          elements.progress.removeAttribute("max");
          elements.progress.removeAttribute("value");
        }
      })
      .fail(function () {
        console.error("error");
      });
  }

  function getOptions() {
    const templateElements = getTemplateElements();

    request("media-clean-get-options", {})
      .done(function (response) {
        for (const option of response) {
          switch (option.option_key) {
            case "status":
              templateElements.status.innerHTML = option.option_value;
              if (option.option_value.startsWith("process")) {
                templateElements.progress.parentNode.classList.add("show");
                templateElements.refreshBtn.setAttribute("disabled", "");
                templateElements.purgeBtn.setAttribute("disabled", "");
              }
              if (option.option_value.startsWith("finish")) {
                templateElements.progress.parentNode.classList.remove("show");
                templateElements.refreshBtn.removeAttribute("disabled");
                templateElements.purgeBtn.removeAttribute("disabled");
              }
              break;
            case "last_update":
              const date = new Date(option.option_value);
              templateElements.lastUpdate.innerHTML = date.toString();
              break;
            case "count":
              templateElements.count.innerHTML = option.option_value;
              break;
          }
        }
      })
      .fail(function () {
        console.error("error");
      });
  }

  function initPanel() {
    const panel = document.querySelector("[data-fpm-media-cleaner]");
    if (!panel) {
      return;
    }

    setInterval(getCount, 5000);
    setInterval(getOptions, 5000);
    getCount();
    getOptions();

    request("media-clean-get-cache", {})
      .done(function (response) {
        const table = document.querySelector("[data-clean-media]");
        if (!table) {
          return alert("Fehler");
        }
        const tbody = table.querySelector("tbody");
        for (const row of response) {
          const tr = document.createElement("tr");
          tr.appendChild(createDataTableTd(row.id));
          const imgStart = row.guid.indexOf("/uploads");
          tr.appendChild(
            // createDataTableTd(`<img src="${row.guid.slice(imgStart)}">`)
            createDataTableTd(row.guid)
          );
          tr.appendChild(createDataTableTd(row.post_modified));
          tr.appendChild(createDataTableTd(row.post_status));
          tr.appendChild(createDataTableTd(row.post_title));
          tr.appendChild(createDataTableTd(row.post_type));
          tbody.appendChild(tr);
        }
      })
      .fail(function () {
        console.error("error");
      });

    document
      .querySelector("[data-fpm-media-cleaner-refresh]")
      .addEventListener("click", function () {
        request("media-clean-fill-cache", {})
          .done(function (response) {
            console.log("safty", response);
          })
          .fail(function () {
            console.error("error");
          });
      });
    document
      .querySelector("[data-fpm-media-cleaner-remove]")
      .addEventListener("click", function () {
        request("media-clean-remove", {})
          .done(function (response) {})
          .fail(function () {
            console.error("error");
          });
      });
  }

  // 'ajaxurl' is allways defined and point to admin
  $(function () {
    initPanel();
  });
  console.log(ajaxurl);
})(jQuery);
