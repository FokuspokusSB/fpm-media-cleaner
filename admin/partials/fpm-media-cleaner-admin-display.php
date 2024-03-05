<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://fokuspokus-media.de
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="m-fpm-media-cleaner" data-fpm-media-cleaner="">
  <section class="m-fpm-media-cleaner__title">
    <div class="m-fpm-media-cleaner__wrapper">
      <h1>
        <?= file_get_contents(FPM_MEDIA_CLEANER_ROOT_DIR . "img/logo.svg") ?>
      </h1>
      <span>
        v.<?= FPM_MEDIA_CLEANER_VERSION ?>
      </span>
    </div>
  </section>

  <section class="m-fpm-media-cleaner__content">
    <div class="m-fpm-media-cleaner__wrapper">
      <div class="m-fpm-media-cleaner__app">

        <h2 class="m-fpm-media-cleaner__content-title">
          <?php esc_html_e("Verlauf", "fpm-media-cleaner"); ?>
        </h2>

        <table class="wp-list-table widefat fixed striped table-view-list">
          <thead>
            <tr>
              <th class="manage-column column-primary">
                <?php esc_html_e("Status", "fpm-media-cleaner"); ?>
              </th>
              <th class="manage-column">
                <?php esc_html_e(
                  "Letzte Aktualisierung",
                  "fpm-media-cleaner"
                ); ?>
              </th>
              <th class="manage-column">
                <?php esc_html_e(
                  "Anzahl der zuletzt gelöschten Bilder",
                  "fpm-media-cleaner"
                ); ?>
              </th>
              <th class="manage-column">
                <?php esc_html_e("Log", "fpm-media-cleaner"); ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="column-primary">
                <span data-options-status="">
                  <div class="loading"><div></div><div></div><div></div><div></div></div>
                </span>
              </td>
              <td>
                <span data-options-last-update="">
                  <div class="loading"><div></div><div></div><div></div><div></div></div>
                </span>
              </td>
              <td>
                <span data-options-count="">
                  <div class="loading"><div></div><div></div><div></div><div></div></div>
                </span>
              </td>
              <td>
                <!-- <button type="button" class="btn icon" disabled="">
                  <i class="dashicons-before dashicons-editor-ul"></i>
                  <?php esc_html_e("Log ansehen", "fpm-media-cleaner"); ?>
                </button> -->
              </td>
            </tr>
          </tbody>

          <tbody data-log-items="">
          </tbody>
          
          <tfoot>
            <tr>
              <td colspan="3">
              </td>
              <td class="column-primary">
                <!-- text-align: right -->
                <button 
                  class="button-primary" 
                  type="button" 
                  data-reset-log=""
                >
                  <?php esc_html_e("Verlauf leeren", "fpm-media-cleaner"); ?>
                </button>
              </td>
            </tr>
          </tfoot>
        </table>

        <h2 class="m-fpm-media-cleaner__content-title">
          <?php esc_html_e("Mediathek aufräumen", "fpm-media-cleaner"); ?>
        </h2>

        <table class="wp-list-table widefat fixed striped table-view-list">
          <thead>
            <tr>
              <th class="manage-column">
                <?php esc_html_e("Ausnahmen festlegen", "fpm-media-cleaner"); ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr style="background-color: inherit;">
              <td>
                <?php esc_html_e(
                  "Optional: Wählen Sie Bilder/Dateien aus, die nicht gelöscht werden sollen, unabhängig von ihrer Benutzung.",
                  "fpm-media-cleaner"
                ); ?>
              </td>
            </tr>
            <tr>
              <td>
                <span data-options-skip-image="" class="image-list">
                  <div class="loading"><div></div><div></div><div></div><div></div></div>
                </span>
              </td>
            </tr>
          </tbody>
          
          <tfoot>
            <tr>
              <td>
                <button type="button" data-add-skip-images="" class="button-primary">
                  <?php esc_html_e(
                    "Ausnahmen aus Meditahek auswählen",
                    "fpm-media-cleaner"
                  ); ?>
                </button>
                <button type="button" data-clear-skip="" class="button-secondary">
                  <?php esc_html_e("Auswahl entfernen", "fpm-media-cleaner"); ?>
                </button>
              </td>
            </tr>
          </tfoot>
        </table>

        <?php if ($external_plugins["filebird"]): ?>
          <table 
            class="wp-list-table widefat fixed striped table-view-list"
            data-init-filebird=""
          >
            <thead>
              <tr>
                <th class="manage-column">
                  <?php esc_html_e(
                    "Filebird-Ordner als Ausnahmen festlegen",
                    "fpm-media-cleaner"
                  ); ?>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr style="background-color: inherit;">
                <td>
                  <?php esc_html_e(
                    "Sie haben das Filebird-Plugin aktiviert. Hier können Sie optional auch Ordner festlegen, deren Inhalt nicht gelöscht werden soll, unabhängig von der Benutzung.",
                    "fpm-media-cleaner"
                  ); ?>
                </td>
              </tr>
              <tr>
                <td>
                  <div data-options-skip-filebird-folder="" class="chip-list">
                    <div class="loading"><div></div><div></div><div></div><div></div></div>
                  </div>
                </td>
              </tr>
            </tbody>
            
            <tfoot>
              <tr>
                <td>
                  <button 
                    type="button" 
                    data-select-filebird-folder="" 
                    class="button-primary"
                  >
                    <?php esc_html_e(
                      "Ordner aus Meditahek auswählen/bearbeiten",
                      "fpm-media-cleaner"
                    ); ?>
                  </button>
                </td>
              </tr>
            </tfoot>
          </table>
        <?php endif; ?>

        <table class="wp-list-table widefat fixed striped table-view-list">
          <thead>
            <tr>
              <th class="manage-column">
                <?php esc_html_e(
                  "Mediathek analysieren",
                  "fpm-media-cleaner"
                ); ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr style="background-color: inherit;">
              <td>
                <?php esc_html_e(
                  "Klicken auf den Button um deine Mediathek nach ungenutzten Dateien zu durchsuchen. Wiederhole diesen Schritt wenn du neue Ausnahmen ob hinzufügst. Je nach umfang der Mediathek kann dieser Prozess etwas Zeit in Anspruch nehmen.",
                  "fpm-media-cleaner"
                ); ?>
              </td>
            </tr>
            <tr>
              <td>
                <div class="m-fpm-media-cleaner__cache-controls">
                  <button type="button" data-fill-cache="" class="button-primary">
                    <?php esc_html_e(
                      "Mediathek analysieren",
                      "fpm-media-cleaner"
                    ); ?>
                  </button>
                  <button 
                    type="button" 
                    data-media-zip="" 
                    class="button button-primary"
                  >
                    <?php esc_html_e(
                      "erstelle ZIP Datei",
                      "fpm-media-cleaner"
                    ); ?>
                  </button>
                  <button 
                    type="button" 
                    data-get-media-zip=""
                    class="button button-secondary"
                  >
                    <?php esc_html_e(
                      "zeige alle ZIP Dateien",
                      "fpm-media-cleaner"
                    ); ?>
                  </button>
                  <span>
                    <?php esc_html_e(
                      "Ungenutzte Dateien gefunden:",
                      "fpm-media-cleaner"
                    ); ?>
                    <span data-cache-total=""></span>
                  </span>
                </div>

              </td>
            </tr>
            <tr style="background-color: inherit;">
              <td style="padding: 0">
                <div class="progressbar">
                  <progress data-progress="">
                  </progress>
                </div>

                <table 
                  class="wp-list-table widefat fixed striped table-view-list m-fpm-media-cleaner__cache-table"
                  data-clean-media=""
                  data-limit="20"
                  data-page="1"
                >
                  <thead>
                    <tr>
                      <th class="manage-column" style="width: 60px;">
                        <?php esc_html_e("ID", "fpm-media-cleaner"); ?>
                      </th>
                      <th class="manage-column column-primary" style="width: 60px;">
                        <?php esc_html_e("Bild", "fpm-media-cleaner"); ?>
                      </th>
                      <th class="manage-column ">
                        <?php esc_html_e("Titel", "fpm-media-cleaner"); ?>
                      </th>
                      <th class="manage-column" style="width: 140px;">
                        <?php esc_html_e(
                          "Aktualisiert am",
                          "fpm-media-cleaner"
                        ); ?>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="4">
                        <div class="loading"><div></div><div></div><div></div><div></div></div>
                      </td>
                    </tr>
                  </tbody>
                </table>

              </td>
            </tr>
          </tbody>
          
          <tfoot>
            <tr>
              <td>
                <ul data-cache-pagination="" class="m-fpm-media-cleaner__pagination">
                </ul>
              </td>
            </tr>
          </tfoot>
        </table>

        <div class="m-fpm-media-cleaner__footer">
          <p>
            <?php esc_html_e(
              "Klicke auf den Button um deine Mediathek zu bereinigen und ungenutzte Bilder zu entfernen.",
              "fpm-media-cleaner"
            ); ?>
          </p>

          <div class="m-fpm-media-cleaner__footer-controls">
            <button 
              type="button" 
              data-remove-images="" 
              class="button button-warn"
            >
              <?php esc_html_e("Delete Media Files", "fpm-media-cleaner"); ?>
            </button>
            <span>
              <?php esc_html_e(
                "Noch zu löschende Dateien:",
                "fpm-media-cleaner"
              ); ?>

              <span data-count="">
                <div class="loading"><div></div><div></div><div></div><div></div></div>
              </span>
            </span>
          </div>
        </div>
      </div>

      <div class="m-fpm-media-cleaner__side-bar">
        <div style="height: 300px; background: white;">
        </div>
      </div>

      
    </div>
  </section>


  <script type="application/json" data-js-translations="">
    <?php echo json_encode([
      "Skip Images Select" => __("Skip Images Select", "fpm-media-cleaner"),
      "select" => __("select", "fpm-media-cleaner"),
      "Do you want to delete the pictures?" => __(
        "Do you want to delete the pictures?",
        "fpm-media-cleaner"
      ),
      "Möchtest du alle Logeinträge löschen?" => __(
        "Möchtest du alle Logeinträge löschen?",
        "fpm-media-cleaner"
      ),
      "No data available." => __("No data available.", "fpm-media-cleaner"),
      "Select Filebird Folder" => __(
        "Select Filebird Folder",
        "fpm-media-cleaner"
      ),
      "Auswahl Zip Export" => __("Auswahl Zip Export", "fpm-media-cleaner"),
      "Close" => __("Close", "fpm-media-cleaner"),
      "Save" => __("Save", "fpm-media-cleaner"),
      "Log ansehen" => __("Log ansehen", "fpm-media-cleaner"),

      "STATUS" => [
        "init" => __("init", "fpm-media-cleaner"),
        "process-remove" => __("process-remove", "fpm-media-cleaner"),
        "finish-remove" => __("finish-remove", "fpm-media-cleaner"),
        "process-cache" => __("process-cache", "fpm-media-cleaner"),
        "finish-cache" => __("finish-cache", "fpm-media-cleaner"),
      ],
    ]); ?>
  </script>
</div>
