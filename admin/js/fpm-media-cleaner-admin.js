(function ($) {
  "use strict";
  function log(msg, type = "log") {
    console[type](`----------------------------------`);
    console[type](`[FPM Media Cleaner]`);
    console[type](`${msg}`);
    console[type](`----------------------------------`);
  }

  function formatDate(inputDate) {
    let hour, minutes, date, month, year;
    hour = inputDate.getHours();
    minutes = inputDate.getMinutes();
    date = inputDate.getDate();
    month = inputDate.getMonth() + 1;
    year = inputDate.getFullYear();
    hour = hour.toString().padStart(2, "0");
    minutes = minutes.toString().padStart(2, "0");
    date = date.toString().padStart(2, "0");
    month = month.toString().padStart(2, "0");
    return `${hour}:${minutes} ${date}.${month}.${year}`;
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
      skipImages: document.querySelector("[data-options-skip-image]"),

      progress: document.querySelector("[data-fpm-media-progress]"),
      refreshBtn: document.querySelector("[data-fpm-media-cleaner-refresh]"),
      purgeBtn: document.querySelector("[data-fpm-media-cleaner-remove]"),
      activeCount: document.querySelector("[data-fpm-media-cleaner-count]"),
    };
  }

  function createDataTableTd(value) {
    const td = document.createElement("td");
    if (typeof value === "string") {
      td.innerHTML = value;
    } else {
      td.appendChild(value);
    }
    return td;
  }

  function createImg(value) {
    const img = document.createElement("img");
    img.src = value;
    return img;
  }

  function getCount() {
    const elements = getTemplateElements();
    request("media-clean-get-count", {})
      .done(function (response) {
        elements.activeCount.innerHTML = response.count;
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
              templateElements.lastUpdate.innerHTML = formatDate(date);
              break;
            case "count":
              templateElements.count.innerHTML = option.option_value;
              break;

            case "skip_ids":
              let printedImages = Array.from(
                templateElements.skipImages.querySelectorAll("img")
              );

              if (printedImages.length === 0) {
                templateElements.skipImages.innerHTML = "";
              }
              printedImages = printedImages.map((v) => v.src);
              if (Array.isArray(option.option_value)) {
                for (const image of option.option_value) {
                  if (!printedImages.includes(image[0])) {
                    templateElements.skipImages.appendChild(
                      createImg(image[0])
                    );
                  }
                }
              }
              break;
          }
        }
      })
      .fail(function () {
        console.error("error");
      });
  }

  function getCacheTable() {
    const table = document.querySelector("[data-clean-media]");
    if (!table) {
      return alert("Fehler");
    }
    table.classList.add("is-loading");

    request("media-clean-get-cache", {})
      .done(function (response) {
        table.classList.remove("is-loading");
        const tbody = table.querySelector("tbody");
        tbody.innerHTML = "";
        if (response.length === 0) {
          table.classList.remove("fill");
          const tr = document.createElement("tr");
          const td = createDataTableTd("Keine Daten vorhanden.");
          td.setAttribute("colspan", table.querySelectorAll("th").length);
          tr.appendChild(td);
          tbody.appendChild(tr);
        } else {
          table.classList.add("fill");

          for (const row of response) {
            const tr = document.createElement("tr");
            tr.appendChild(createDataTableTd(row.id));
            // tr.appendChild(createDataTableTd(createImg(row.img[0])));
            tr.appendChild(createDataTableTd(row.img[0]));
            const modified = new Date(row.post_modified);
            tr.appendChild(createDataTableTd(row.post_title));
            const lastTd = createDataTableTd(formatDate(modified));
            lastTd.setAttribute("colspan", "2");
            tr.appendChild(lastTd);
            tbody.appendChild(tr);
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
    getCacheTable();

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
        setTimeout(() => getOptions(), 500);
      });
    document
      .querySelector("[data-fpm-media-cleaner-clear-skip]")
      .addEventListener("click", function () {
        request("media-clean-set-skip", { ids: false }).done(function (
          response
        ) {
          const template = getTemplateElements();
          template.skipImages.innerHTML = "";
          getOptions();
        });
      });
    document
      .querySelector("[data-fpm-media-cleaner-remove]")
      .addEventListener("click", function () {
        const answer = confirm("Möchtest du die Bilder löschen?");
        if (answer) {
          request("media-clean-remove", {})
            .done(function (response) {})
            .fail(function () {
              console.error("error");
            });
          setTimeout(() => getOptions(), 500);
        }
      });
    document
      .querySelector("[data-refresh-cash]")
      .addEventListener("click", function () {
        getCacheTable();
      });

    document
      .querySelector("[data-add-skip-images]")
      .addEventListener("click", function () {
        const mediaWindow = wp.media({
          title: "Bilder die übersprungen werden auswählen",
          library: { type: "image" },
          multiple: true,
          button: { text: "auswählen" },
        });

        mediaWindow.on("select", function () {
          const ids = mediaWindow
            .state()
            .get("selection")
            .toJSON()
            .map((v) => v.id);
          request("media-clean-set-skip", { ids }).done(function (response) {
            getOptions();
          });
        });
        mediaWindow.open();
      });
  }

  $(function () {
    initPanel();
  });
})(jQuery);
