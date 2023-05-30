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
<section class="m-fpm-media-cleaner" data-fpm-media-cleaner="">
  <h1>
    <?php esc_html_e("FPM Media Cleaner", "fpm-media-cleaner"); ?>
  </h1>
  <table>
    <thead>
      <tr>
        <th>
          <?php esc_html_e("Status", "fpm-media-cleaner"); ?>
        </th>
        <th>
          <?php esc_html_e("Last Update", "fpm-media-cleaner"); ?>
        </th>
        <th>
          <?php esc_html_e("Last Update Count", "fpm-media-cleaner"); ?>
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
      </tr>
    </tbody>
  </table>

  <table>
    <thead>
      <tr>
        <th>
          <?php esc_html_e("Skip Images", "fpm-media-cleaner"); ?>
        </th>
        <th style="width:64px;">
          <button type="button" data-add-skip-images="" class="icon">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 -960 960 960" width="48"><path d="M180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h279v60H180v600h600v-279h60v279q0 24-18 42t-42 18H180Zm202-219-42-43 398-398H519v-60h321v321h-60v-218L382-339Z"/></svg>
          </button>
          <button type="button" data-fpm-media-cleaner-clear-skip="" class="icon">
            <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 96 960 960" width="48"><path d="M261 936q-24.75 0-42.375-17.625T201 876V306h-41v-60h188v-30h264v30h188v60h-41v570q0 24-18 42t-42 18H261Zm438-630H261v570h438V306ZM367 790h60V391h-60v399Zm166 0h60V391h-60v399ZM261 306v570-570Z"/></svg>
          </button>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="2">
          <span data-options-skip-image="">
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
          <th style="width:64px;">
            <button type="button" data-select-filebird-folder="" class="icon">
              <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 -960 960 960" width="48"><path d="M180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h279v60H180v600h600v-279h60v279q0 24-18 42t-42 18H180Zm202-219-42-43 398-398H519v-60h321v321h-60v-218L382-339Z"/></svg>
            </button>
            <button type="button" data-clear-filebird-folder="" class="icon">
              <svg fill="white" xmlns="http://www.w3.org/2000/svg" height="30" viewBox="0 96 960 960" width="48"><path d="M261 936q-24.75 0-42.375-17.625T201 876V306h-41v-60h188v-30h264v30h188v60h-41v570q0 24-18 42t-42 18H261Zm438-630H261v570h438V306ZM367 790h60V391h-60v399Zm166 0h60V391h-60v399ZM261 306v570-570Z"/></svg>
            </button>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="2">
            <span data-options-skip-filebird-folder="">
              <div class="loading"><div></div><div></div><div></div><div></div></div>
            </span>
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
    ]); ?>
  </script>



</section>
