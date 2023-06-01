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

      </span>
    </div>
  </section>

  <section class="m-fpm-media-cleaner__content">
    <div class="m-fpm-media-cleaner__wrapper">
      
      <h2 class="m-fpm-media-cleaner__content-title">
        <?php esc_html_e("Verlauf", "fpm-media-cleaner"); ?>
      </h2>

      <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
          <tr>
            <th class="manage-column">
              <?php esc_html_e("Status", "fpm-media-cleaner"); ?>
            </th>
            <th class="manage-column">
              <?php esc_html_e("Letzte Aktualisierung", "fpm-media-cleaner"); ?>
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
            <td>
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
              <button type="button" class="btn icon" disabled="">
                <i class="dashicons-before dashicons-editor-ul"></i>
                <?php esc_html_e("Log ansehen", "fpm-media-cleaner"); ?>
              </button>
            </td>
          </tr>
        </tbody>
        
        <tfoot>
          <tr>
            <td colspan="3">
            </td>
            <td>
              <!-- text-align: right -->
              <button class="button-primary" type="button" disabled="">
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
              <button type="button" data-fpm-media-cleaner-clear-skip="" class="button-secondary">
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
              <?php esc_html_e("Mediathek analysieren", "fpm-media-cleaner"); ?>
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
              <button type="button" data-fpm-media-cleaner-refresh="" class="button-primary">
                <?php esc_html_e(
                  "Mediathek analysieren",
                  "fpm-media-cleaner"
                ); ?>
              </button>
            </td>
          </tr>
          <tr style="background-color: inherit;">
            <td style="padding: 0">
              <div class="progressbar">
                <progress data-fpm-media-progress="">
                </progress>
              </div>

              <table 
                class="wp-list-table widefat fixed striped table-view-list m-fpm-media-cleaner__cache-table"
                data-clean-media=""
              >
                <thead>
                  <tr>
                    <th class="manage-column" style="width: 30px;">
                      <?php esc_html_e("ID", "fpm-media-cleaner"); ?>
                    </th>
                    <th class="manage-column" style="width: 60px;">
                      <?php esc_html_e("Bild", "fpm-media-cleaner"); ?>
                    </th>
                    <th class="manage-column">
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

                </tbody>
              </table>

            </td>
          </tr>
        </tbody>
        
        <tfoot>
          <tr>
            <td>
              PAGGING
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

        <div>
          <button 
            type="button" 
            data-fpm-media-cleaner-remove="" 
            class="button button-warn"
          >
            <?php esc_html_e("Delete Media Files", "fpm-media-cleaner"); ?>
          </button>
          <span>
            <?php esc_html_e(
              "Noch zu löschende Dateien:",
              "fpm-media-cleaner"
            ); ?>

            <span data-fpm-media-cleaner-count="">
              <div class="loading"><div></div><div></div><div></div><div></div></div>
            </span>
          </span>
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
      "No data available." => __("No data available.", "fpm-media-cleaner"),
      "Select Filebird Folder" => __(
        "Select Filebird Folder",
        "fpm-media-cleaner"
      ),
      "Close" => __("Close", "fpm-media-cleaner"),
      "Save" => __("Save", "fpm-media-cleaner"),
    ]); ?>
  </script>
</div>
