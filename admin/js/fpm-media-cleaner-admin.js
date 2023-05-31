(function ($) {
  "use strict";
  let ROOT_DOCUMENT;
  let TRANSLATIONS;

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

      progress: ROOT_DOCUMENT.querySelector("[data-fpm-media-progress]"),
      refreshBtn: ROOT_DOCUMENT.querySelector(
        "[data-fpm-media-cleaner-refresh]"
      ),
      purgeBtn: ROOT_DOCUMENT.querySelector("[data-fpm-media-cleaner-remove]"),
      activeCount: ROOT_DOCUMENT.querySelector(
        "[data-fpm-media-cleaner-count]"
      ),
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
    icon.className = "wp-menu-image dashicons-before dashicons-trash";
    removeElement.appendChild(icon);
    skipImage.appendChild(removeElement);
    return skipImage;
  }

  function removeSingleSkipImage(id) {
    const templateElements = getTemplateElements();
    let ids = templateElements.skipImages.getAttribute("data-ids");
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
        const template = getTemplateElements();
        template.skipImages.innerHTML = "";
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
                templateElements.skipImages.setAttribute(
                  "data-ids",
                  JSON.stringify(option.option_value)
                );
                for (const image of option.option_value) {
                  if (!printedImages.includes(image.src)) {
                    templateElements.skipImages.appendChild(
                      createSkipImg(image)
                    );
                  }
                }

                const optionImageSrcList = option.option_value.map((v) => v[0]);
                for (const printedImageSrc of printedImages) {
                  if (!optionImageSrcList.includes(printedImageSrc)) {
                    const img =
                      templateElements.skipFilebirdFolder.querySelector(
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
                templateElements.skipFilebirdFolder.querySelectorAll(".chip")
              );

              if (printedFolders.length === 0) {
                templateElements.skipFilebirdFolder.innerHTML = "";
              }
              printedFolders = printedFolders.map((v) =>
                v.getAttribute("data-id")
              );

              if (Array.isArray(option.option_value)) {
                // add new folders
                for (const folder of option.option_value) {
                  if (!printedFolders.includes(folder.id)) {
                    templateElements.skipFilebirdFolder.appendChild(
                      createChip(folder)
                    );
                  }
                }
                // remove old folders
                const optionFolderIds = option.option_value.map((v) => v.id);
                for (const printFolderId of printedFolders) {
                  if (!optionFolderIds.includes(printFolderId)) {
                    const chip =
                      templateElements.skipFilebirdFolder.querySelector(
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
    const table = ROOT_DOCUMENT.querySelector("[data-clean-media]");
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
          const td = createDataTableTd(TRANSLATIONS["No data available."]);
          td.setAttribute("colspan", table.querySelectorAll("th").length);
          tr.appendChild(td);
          tbody.appendChild(tr);
        } else {
          table.classList.add("fill");

          for (const row of response) {
            const tr = document.createElement("tr");
            tr.appendChild(createDataTableTd(row.id));
            tr.appendChild(createDataTableTd(createImg(row.img[0])));
            // tr.appendChild(createDataTableTd(row.img[0]));
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

        htmlParent.html("");
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
        var selectDialog = $(`<div class="m-fpm-media-cleaner">
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
                  const template = getTemplateElements();
                  template.skipFilebirdFolder.innerHTML =
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
    if (!ROOT_DOCUMENT) {
      return;
    }

    setInterval(getCount, 5000);
    setInterval(getOptions, 5000);
    getCount();
    getOptions();
    getCacheTable();
    pluginAttach();

    ROOT_DOCUMENT.querySelector(
      "[data-fpm-media-cleaner-refresh]"
    ).addEventListener("click", function () {
      request("media-clean-fill-cache", {})
        .done(function (response) {})
        .fail(function () {});
      setTimeout(() => getOptions(), 500);
    });
    ROOT_DOCUMENT.querySelector(
      "[data-fpm-media-cleaner-clear-skip]"
    ).addEventListener("click", function () {
      request("media-clean-set-skip", { ids: false }).done(function (response) {
        const template = getTemplateElements();
        template.skipImages.innerHTML = getLoadingElementString();
        getOptions();
      });
    });
    ROOT_DOCUMENT.querySelector(
      "[data-fpm-media-cleaner-remove]"
    ).addEventListener("click", function () {
      const answer = confirm(
        TRANSLATIONS["Do you want to delete the pictures?"]
      );
      if (answer) {
        request("media-clean-remove", {})
          .done(function (response) {})
          .fail(function () {
            console.error("error");
          });
        setTimeout(() => getOptions(), 500);
      }
    });
    ROOT_DOCUMENT.querySelector("[data-refresh-cash]").addEventListener(
      "click",
      function () {
        getCacheTable();
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
  }

  $(function () {
    // fill global vars
    ROOT_DOCUMENT = document.querySelector("[data-fpm-media-cleaner]");
    try {
      TRANSLATIONS = JSON.parse(
        ROOT_DOCUMENT.querySelector("[data-js-translations]").innerHTML
      );
    } catch (e) {
      log(e.message, "error");
    }

    initPanel();
  });
})(jQuery);
