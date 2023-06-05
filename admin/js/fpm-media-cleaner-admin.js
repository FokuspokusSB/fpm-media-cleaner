(function ($) {
  "use strict";
  let ROOT_DOCUMENT;
  let TRANSLATIONS;
  let TEMPLATE_ELEMENTS;

  function log(msg, type = "log") {
    console[type](`----------------------------------`);
    console[type](`[FPM Media Cleaner]`);
    console[type](`${msg}`);
    console[type](`----------------------------------`);
  }

  function getLoadingElementString() {
    return '<div class="loading"><div></div><div></div><div></div><div></div></div>';
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
      status: ROOT_DOCUMENT.querySelector("[data-options-status]"),
      lastUpdate: ROOT_DOCUMENT.querySelector("[data-options-last-update]"),
      count: ROOT_DOCUMENT.querySelector("[data-options-count]"),
      skipImages: ROOT_DOCUMENT.querySelector("[data-options-skip-image]"),
      skipFilebirdFolder: ROOT_DOCUMENT.querySelector(
        "[data-options-skip-filebird-folder]"
      ),

      logTbody: ROOT_DOCUMENT.querySelector("[data-log-items]"),
      resetLog: ROOT_DOCUMENT.querySelector("[data-reset-log]"),

      progress: ROOT_DOCUMENT.querySelector("[data-progress]"),
      activeCount: ROOT_DOCUMENT.querySelector("[data-count]"),

      purgeBtn: ROOT_DOCUMENT.querySelector("[data-remove-images]"),
      refreshBtn: ROOT_DOCUMENT.querySelector("[data-fill-cache]"),
      zipMediaBtn: ROOT_DOCUMENT.querySelector("[data-media-zip]"),
      getZipMediaBtn: ROOT_DOCUMENT.querySelector("[data-get-media-zip]"),

      cacheTable: ROOT_DOCUMENT.querySelector("[data-clean-media]"),
      cacheTotal: ROOT_DOCUMENT.querySelector("[data-cache-total]"),
      cachePagination: ROOT_DOCUMENT.querySelector("[data-cache-pagination]"),
    };
  }

  function createTableTd(value) {
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
    if (value) {
      img.src = value;
    }
    return img;
  }

  function createSkipImg(value) {
    const skipImage = document.createElement("span");
    skipImage.setAttribute("data-id", value.id);
    skipImage.classList.add("skip-image");
    skipImage.appendChild(createImg(value.src));
    const removeElement = document.createElement("button");
    removeElement.addEventListener("click", function () {
      removeSingleSkipImage(value.id);
    });
    removeElement.classList.add("remove");
    const icon = document.createElement("i");
    removeElement.appendChild(icon);
    skipImage.appendChild(removeElement);
    return skipImage;
  }

  function removeSingleSkipImage(id) {
    let ids = TEMPLATE_ELEMENTS.skipImages.getAttribute("data-ids");
    if (!ids) {
      return;
    }
    try {
      ids = JSON.parse(ids).map((v) => v.id);
    } catch (e) {
      console.error(e);
      return;
    }

    const removeIndex = ids.indexOf(id);
    if (removeIndex >= 0) {
      ids.splice(removeIndex, 1);
      if (ids.length === 0) {
        ids = false;
      }
      request("media-clean-set-skip", { ids }).done(function (response) {
        TEMPLATE_ELEMENTS.skipImages.innerHTML = getLoadingElementString();
        getOptions();
      });
    }
  }

  function createChip(value) {
    const chip = document.createElement("span");
    chip.classList.add("chip");
    const i = document.createElement("i");
    i.className = "wp-menu-image dashicons-before dashicons-open-folder";
    chip.appendChild(i);
    const textSpan = document.createElement("span");
    textSpan.innerHTML = value.name;
    chip.appendChild(textSpan);
    if (value.count) {
      const countSpan = document.createElement("span");
      countSpan.classList.add("count");
      countSpan.innerHTML = value.count;
      chip.appendChild(countSpan);
    }
    chip.setAttribute("data-id", value.id);
    return chip;
  }

  function clickPageCacheTable() {
    const page = this.getAttribute("data-page");
    TEMPLATE_ELEMENTS.cacheTable.setAttribute("data-page", page);
    getCacheTable();
  }
  function setPaginationElements() {
    const total =
      TEMPLATE_ELEMENTS.cacheTable.getAttribute("data-total") || "0";
    const limit = TEMPLATE_ELEMENTS.cacheTable.getAttribute("data-limit");
    const activePage = TEMPLATE_ELEMENTS.cacheTable.getAttribute("data-page");
    const pages = Math.ceil(total / limit);
    const ul = TEMPLATE_ELEMENTS.cachePagination;
    ul.innerHTML = "";

    for (let page = 1; page <= pages; page++) {
      const li = document.createElement("li");
      const btn = document.createElement("button");
      if (activePage == page) {
        btn.classList.add("active");
      }
      btn.setAttribute("data-page", page);
      btn.innerHTML = page;
      btn.addEventListener("click", clickPageCacheTable);
      li.appendChild(btn);
      ul.appendChild(li);
    }
  }

  function getCount() {
    request("media-clean-get-count", {})
      .done(function (response) {
        TEMPLATE_ELEMENTS.activeCount.innerHTML = response.count;
        const allCounts = Number.parseInt(TEMPLATE_ELEMENTS.count.innerHTML);
        const count = Number.parseInt(response);

        if (!Number.isNaN(allCounts) && !Number.isNaN(count)) {
          TEMPLATE_ELEMENTS.progress.setAttribute("max", allCounts);
          TEMPLATE_ELEMENTS.progress.setAttribute("value", allCounts - count);
        } else {
          TEMPLATE_ELEMENTS.progress.removeAttribute("max");
          TEMPLATE_ELEMENTS.progress.removeAttribute("value");
        }
      })
      .fail(function () {
        console.error("error");
      });
  }

  function getLog() {
    function setBtnClick(btn, log) {
      btn.innerHTML = `<i class="dashicons-before dashicons-editor-ul"></i>${TRANSLATIONS["Log ansehen"]}`;
      btn.addEventListener("click", function () {
        alert(log);
      });
      btn.className = "btn icon";
    }
    TEMPLATE_ELEMENTS.logTbody.innerHTML = `<tr>
      <td colspan="4">
        ${getLoadingElementString()}
      </td>
    </tr>`;

    request("media-clean-get-log", {}).done(function (response) {
      if (Array.isArray(response)) {
        TEMPLATE_ELEMENTS.logTbody.innerHTML = "";
        for (const item of response) {
          const tr = document.createElement("tr");
          const date = new Date(item.insert_date);
          const btn = document.createElement("button");
          setBtnClick(btn, item.log);

          tr.append(createTableTd(TRANSLATIONS["STATUS"][item.status]));
          tr.append(createTableTd(formatDate(date)));
          tr.append(createTableTd(item.count));
          tr.append(createTableTd(btn));

          TEMPLATE_ELEMENTS.logTbody.append(tr);
        }
      }
    });
  }

  function getOptions() {
    request("media-clean-get-options", {})
      .done(function (response) {
        for (const option of response) {
          switch (option.option_key) {
            case "status":
              TEMPLATE_ELEMENTS.status.innerHTML =
                TRANSLATIONS["STATUS"][option.option_value];
              if (option.option_value.startsWith("process")) {
                TEMPLATE_ELEMENTS.progress.parentNode.classList.add("show");
                TEMPLATE_ELEMENTS.refreshBtn.setAttribute("disabled", "");
                TEMPLATE_ELEMENTS.purgeBtn.setAttribute("disabled", "");
              }
              if (option.option_value.startsWith("finish")) {
                TEMPLATE_ELEMENTS.progress.parentNode.classList.remove("show");
                TEMPLATE_ELEMENTS.refreshBtn.removeAttribute("disabled");
                TEMPLATE_ELEMENTS.purgeBtn.removeAttribute("disabled");
              }
              break;
            case "last_update":
              const date = new Date(option.option_value);
              TEMPLATE_ELEMENTS.lastUpdate.innerHTML = formatDate(date);
              break;
            case "count":
              TEMPLATE_ELEMENTS.count.innerHTML = option.option_value;
              break;

            case "skip_ids":
              let printedImages = Array.from(
                TEMPLATE_ELEMENTS.skipImages.querySelectorAll("img")
              );

              if (printedImages.length === 0) {
                TEMPLATE_ELEMENTS.skipImages.innerHTML = "";
              }
              printedImages = printedImages.map((v) => v.src);
              if (Array.isArray(option.option_value)) {
                TEMPLATE_ELEMENTS.skipImages.setAttribute(
                  "data-ids",
                  JSON.stringify(option.option_value)
                );
                for (const image of option.option_value) {
                  if (!printedImages.includes(image.src)) {
                    TEMPLATE_ELEMENTS.skipImages.appendChild(
                      createSkipImg(image)
                    );
                  }
                }

                const optionImageSrcList = option.option_value.map((v) => v[0]);
                for (const printedImageSrc of printedImages) {
                  if (!optionImageSrcList.includes(printedImageSrc)) {
                    const img =
                      TEMPLATE_ELEMENTS.skipFilebirdFolder.querySelector(
                        `img[src="${printedImageSrc}"]`
                      );
                    if (img) {
                      img.parentNode.removeChild(img);
                    }
                  }
                }
              }
              break;
            case "external_plugin_filebird_ids":
              let printedFolders = Array.from(
                TEMPLATE_ELEMENTS.skipFilebirdFolder.querySelectorAll(".chip")
              );

              if (printedFolders.length === 0) {
                TEMPLATE_ELEMENTS.skipFilebirdFolder.innerHTML = "";
              }
              printedFolders = printedFolders.map((v) =>
                v.getAttribute("data-id")
              );

              if (Array.isArray(option.option_value)) {
                // add new folders
                for (const folder of option.option_value) {
                  if (!printedFolders.includes(folder.id)) {
                    TEMPLATE_ELEMENTS.skipFilebirdFolder.appendChild(
                      createChip(folder)
                    );
                  }
                }
                // remove old folders
                const optionFolderIds = option.option_value.map((v) => v.id);
                for (const printFolderId of printedFolders) {
                  if (!optionFolderIds.includes(printFolderId)) {
                    const chip =
                      TEMPLATE_ELEMENTS.skipFilebirdFolder.querySelector(
                        `.chip[data-id="${printFolderId}"]`
                      );
                    if (chip) {
                      chip.parentNode.removeChild(chip);
                    }
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
    const table = TEMPLATE_ELEMENTS.cacheTable;
    if (!table) {
      return alert("Fehler");
    }
    table.classList.add("is-loading");
    TEMPLATE_ELEMENTS.cacheTotal.innerHTML = getLoadingElementString();
    const page = table.getAttribute("data-page") || 1;
    const limit = table.getAttribute("data-limit") || 20;

    request("media-clean-get-cache", { page, limit })
      .done(function (response) {
        if (!response.data) {
          return;
        }
        TEMPLATE_ELEMENTS.cacheTotal.innerHTML = response.total;
        table.setAttribute("data-total", response.total);
        setPaginationElements();

        const data = response.data;
        table.classList.remove("is-loading");
        const tbody = table.querySelector("tbody");
        tbody.innerHTML = "";
        if (data.length === 0) {
          table.classList.remove("fill");
          const tr = document.createElement("tr");
          const td = createTableTd(TRANSLATIONS["No data available."]);
          td.setAttribute("colspan", table.querySelectorAll("th").length);
          tr.appendChild(td);
          tbody.appendChild(tr);
        } else {
          table.classList.add("fill");

          for (const row of data) {
            const tr = document.createElement("tr");
            const modified = new Date(row.post_modified);
            tr.appendChild(createTableTd(row.id));
            const imgTd = createTableTd(createImg(row.img[0] || ""));
            imgTd.classList.add("column-primary");
            tr.appendChild(imgTd);
            tr.appendChild(createTableTd(row.post_title));
            tr.appendChild(createTableTd(formatDate(modified)));
            tbody.appendChild(tr);
          }
        }

        // TODO: add pagination
      })
      .fail(function () {
        console.error("error");
      });
  }

  function pluginAttach() {
    function initFilebird() {
      let selectedFolderIds = [];
      function buildCheckboxTree(htmlParent, list) {
        function translateListData(list) {
          const resultList = [];
          for (const item of list) {
            const resultItem = {
              id: item.id,
              text: item.name,
              state: {
                opened: true,
                selected: item.selected,
              },
              children: [],
            };
            if (item.children) {
              resultItem.children = translateListData(item.children);
            }
            resultList.push(resultItem);
          }
          return resultList;
        }

        htmlParent.html(getLoadingElementString());
        const jsTreeData = translateListData(list);
        htmlParent
          .on("changed.jstree", function (e, data) {
            selectedFolderIds = [];
            for (let i = 0; i < data.selected.length; i++) {
              selectedFolderIds.push(
                Number.parseInt(data.instance.get_node(data.selected[i]).id)
              );
            }
          })
          .jstree({
            core: {
              themes: {
                variant: "large",
              },
              data: jsTreeData,
            },
            checkbox: {
              keep_selected_style: false,
            },
            plugins: ["wholerow", "checkbox"],
          });
      }

      ROOT_DOCUMENT.querySelector(
        "[data-select-filebird-folder]"
      ).addEventListener("click", function () {
        var selectDialog = $(`<div class="m-fpm-media-cleaner__dialog">
          <div class="dialog-content jstree-filebird" data-content="">
            <center>
              ${getLoadingElementString()}
            </center>
          </div>
        </div>`);
        selectDialog.dialog({
          title: TRANSLATIONS["Select Filebird Folder"],
          dialogClass: "wp-dialog wp-core-ui",
          draggable: false,
          modal: true,
          autoOpen: false,
          closeOnEscape: true,
          width: 400,
          buttons: [
            {
              // icon: "ui-icon-heart",
              text: TRANSLATIONS["Close"],
              click: function () {
                selectDialog.dialog("close");
              },
            },
            {
              // icon: "ui-icon-heart",
              text: TRANSLATIONS["Save"],
              click: function () {
                selectDialog.find("[data-content]").html(`<center>
                  <div class="loading"><div></div><div></div><div></div><div></div></div>
                </center>`);
                let ids = selectedFolderIds;
                if (ids.length === 0) {
                  ids = false;
                }
                request("media-clean-set-filebird-folders", {
                  ids,
                }).done(() => {
                  TEMPLATE_ELEMENTS.skipFilebirdFolder.innerHTML =
                    getLoadingElementString();
                  getOptions();
                  selectDialog.dialog("close");
                });
              },
            },
          ],
          open: function (event, ui) {
            request("media-clean-get-filebird-folders").done(function (
              response
            ) {
              buildCheckboxTree(selectDialog.find("[data-content]"), response);
            });
          },
        });
        selectDialog.dialog("open");
      });
    }

    if (ROOT_DOCUMENT.querySelector("[data-init-filebird]")) {
      initFilebird();
    }
  }

  function initPanel() {
    setInterval(getCount, 5000);
    setInterval(getOptions, 5000);
    getCount();
    getOptions();
    getCacheTable();
    getLog();
    pluginAttach();

    TEMPLATE_ELEMENTS.refreshBtn.addEventListener("click", function () {
      const table = ROOT_DOCUMENT.querySelector("[data-clean-media]");
      if (table) {
        table.classList.add("is-loading");
      }

      request("media-clean-fill-cache", {})
        .done(function (response) {
          getCacheTable();
        })
        .fail(function () {});
      setTimeout(() => getOptions(), 500);
    });

    ROOT_DOCUMENT.querySelector("[data-clear-skip]").addEventListener(
      "click",
      function () {
        request("media-clean-set-skip", { ids: false }).done(function (
          response
        ) {
          TEMPLATE_ELEMENTS.skipImages.innerHTML = getLoadingElementString();
          getOptions();
        });
      }
    );

    ROOT_DOCUMENT.querySelector("[data-remove-images]").addEventListener(
      "click",
      function () {
        const answer = confirm(
          TRANSLATIONS["Do you want to delete the pictures?"]
        );
        if (answer) {
          request("media-clean-remove", {})
            .done(function (response) {
              getCacheTable();
            })
            .fail(function () {
              console.error("error");
            });
          setTimeout(() => getOptions(), 500);
        }
      }
    );

    ROOT_DOCUMENT.querySelector("[data-add-skip-images]").addEventListener(
      "click",
      function () {
        const mediaWindow = wp.media({
          title: TRANSLATIONS["Skip Images Select"],
          library: { type: "image" },
          multiple: true,
          button: { text: TRANSLATIONS["select"] },
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
      }
    );

    TEMPLATE_ELEMENTS.zipMediaBtn.addEventListener("click", function () {
      TEMPLATE_ELEMENTS.zipMediaBtn.setAttribute("disabled", "disabled");
      const text = TEMPLATE_ELEMENTS.zipMediaBtn.innerHTML;
      TEMPLATE_ELEMENTS.zipMediaBtn.innerHTML = getLoadingElementString();
      request("media-clean-zip").done(function (response) {
        TEMPLATE_ELEMENTS.zipMediaBtn.removeAttribute("disabled");
        TEMPLATE_ELEMENTS.zipMediaBtn.innerHTML = text;
      });
    });

    TEMPLATE_ELEMENTS.getZipMediaBtn.addEventListener("click", function () {
      var selectDialog = $(`<div class="m-fpm-media-cleaner__dialog">
          <div class="dialog-content" data-content="">
            <center>
              ${getLoadingElementString()}
            </center>
          </div>
        </div>`);
      selectDialog.dialog({
        title: TRANSLATIONS["Auswahl Zip Export"],
        dialogClass: "wp-dialog wp-core-ui",
        draggable: false,
        modal: true,
        autoOpen: false,
        closeOnEscape: true,
        width: 400,
        buttons: [
          {
            text: TRANSLATIONS["Close"],
            click: function () {
              selectDialog.dialog("close");
            },
          },
        ],
        open: function (event, ui) {
          request("media-clean-get-zip").done(function (response) {
            if (Array.isArray(response)) {
              const content = selectDialog.find("[data-content]");
              content.html("");
              for (const exportZip of response) {
                const row = $(`
                  <p>
                    <a href="${exportZip.url}" target="_blank">
                      ${exportZip.filename}
                      <span style="display: inline-block; width: 100%;">${formatDate(
                        new Date(Number.parseInt(exportZip.date) * 1000)
                      )}</span>
                    </a>
                    <button data-remove-zip="">
                      Remove
                    </button>
                  </p>
                `);
                row.find("[data-remove-zip]").on("click", function () {
                  request("media-clean-remove-zip", {
                    zip: exportZip.filename,
                  }).done(function () {
                    row.remove();
                  });
                });
                content.append(row);
              }
            }
          });
        },
      });
      selectDialog.dialog("open");
    });

    TEMPLATE_ELEMENTS.resetLog.addEventListener("click", function () {
      const answer = confirm(
        TRANSLATIONS["Möchtest du alle Logeinträge löschen?"]
      );
      if (!answer) {
        return;
      }
      request("media-clean-reset-log").done(function (response) {
        TEMPLATE_ELEMENTS.logTbody.innerHTML = "";
      });
    });
  }

  $(function () {
    // fill global vars
    ROOT_DOCUMENT = document.querySelector("[data-fpm-media-cleaner]");
    if (!ROOT_DOCUMENT) {
      return;
    }

    try {
      TRANSLATIONS = JSON.parse(
        ROOT_DOCUMENT.querySelector("[data-js-translations]").innerHTML
      );
    } catch (e) {
      log(e.message, "error");
    }
    TEMPLATE_ELEMENTS = getTemplateElements();

    initPanel();
  });
})(jQuery);
