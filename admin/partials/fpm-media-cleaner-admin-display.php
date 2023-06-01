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
    </div>
  </section>

  <section class="m-fpm-media-cleaner__content">
    <div class="m-fpm-media-cleaner__wrapper">
      
      <h2 class="m-fpm-media-cleaner__content-title">
        <?php esc_html_e("Verlauf", "fpm-media-cleaner"); ?>
      </h2>

      <table class="wp-list-table widefat fixed striped table-view-list m-fpm-media-cleaner__history">
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
          <?php for ($i = 0; $i < 5; $i++): ?>
            <tr>
              <td>
              Lorem Ipsum
              </td>
              <td>
              31.05.2023 14:23 Uhr
              </td>
              <td>
              27
              </td>
              <td>
                <button type="button" class="btn icon">
                  <i class="dashicons-before dashicons-editor-ul"></i>
                  <?php esc_html_e("Log ansehen", "fpm-media-cleaner"); ?>
                </button>
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
        
        <tfoot>
          <tr>
            <td colspan="3">
            </td>
            <td>
              <!-- text-align: right -->
              <button class="button-primary" type="button">
                <?php esc_html_e("Verlauf leeren", "fpm-media-cleaner"); ?>
              </button>
            </td>
          </tr>
        </tfoot>
      </table>


      <h2 class="m-fpm-media-cleaner__content-title">
        <?php esc_html_e("Verlauf", "fpm-media-cleaner"); ?>
      </h2>

      <table class="wp-list-table widefat fixed striped table-view-list m-fpm-media-cleaner__history">
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

    </div>



  </section>




  <table>
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
      </tr>
    </tbody>
  </table>

  <?php if ($external_plugins["filebird"]): ?>
    <table data-init-filebird="">
      <thead>
        <tr>
          <th>
            <?php esc_html_e("Skip Filebird Folder", "fpm-media-cleaner"); ?>
          </th>
          <th style="width:30px;">
            <button type="button" data-select-filebird-folder="" class="icon">
              <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 -960 960 960" width="48"><path d="M180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h279v60H180v600h600v-279h60v279q0 24-18 42t-42 18H180Zm202-219-42-43 398-398H519v-60h321v321h-60v-218L382-339Z"/></svg>
            </button>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="2">
            <div data-options-skip-filebird-folder="" class="chip-list">
              <div class="loading"><div></div><div></div><div></div><div></div></div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

  <?php endif; ?>

  <div class="controls">
    <div>
      <button type="button" data-fpm-media-cleaner-refresh="">
        <?php esc_html_e("Fill/Update Cache Table", "fpm-media-cleaner"); ?>
      </button>

      <button type="button" data-fpm-media-cleaner-remove="" class="warn">
        <?php esc_html_e("Delete Media Files", "fpm-media-cleaner"); ?>
      </button>
    </div>

    <span>
      <?php esc_html_e("Current Count:", "fpm-media-cleaner"); ?>
      <strong>
        <span data-fpm-media-cleaner-count="">
          <div class="loading"><div></div><div></div><div></div><div></div></div>
        </span>
      </strong>
    </span>
  </div>

  <div class="progressbar">
    <progress data-fpm-media-progress="">
    </progress>
  </div>

  <table data-clean-media="" class="data">
    <thead>
      <tr>
        <th style="width: 50px;">
          <?php esc_html_e("ID", "fpm-media-cleaner"); ?>
        </th>
        <th style="width: 70px;">
          <?php esc_html_e("Image", "fpm-media-cleaner"); ?>
        </th>
        <th style="width: auto">
          <?php esc_html_e("Title", "fpm-media-cleaner"); ?>
        </th>
        <th style="width: 85px;">
          <?php esc_html_e("Updated on", "fpm-media-cleaner"); ?>
        </th>
        <th style="width: 30px;">
          <button type="button" class="icon" data-refresh-cash="">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 96 960 960" width="48"><path d="M480 896q-133 0-226.5-93.5T160 576q0-133 93.5-226.5T480 256q85 0 149 34.5T740 385V256h60v254H546v-60h168q-38-60-97-97t-137-37q-109 0-184.5 75.5T220 576q0 109 75.5 184.5T480 836q83 0 152-47.5T728 663h62q-29 105-115 169t-195 64Z"/></svg>
          </button>
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
