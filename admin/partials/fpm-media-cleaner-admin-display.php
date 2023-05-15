<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://samyblake.ninja
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<section class="m-fpm-media-cleaner" data-fpm-media-cleaner="">
  <h1>
    FPM Media Cleaner
  </h1>
  <table border="1">
    <tr>
      <th>
        Status
      </th>
      <th>
        Last Update
      </th>
    </tr>
    <tr>
      <td>
        <span data-options-status=""></span>
      </td>
      <td>
        <span data-options-last-update=""></span>
      </td>
    </tr>
  </table>
  <div>
    <button type="button" data-fpm-media-cleaner-refresh="">
      REFRESH
    </button>

    <button type="button" data-fpm-media-cleaner-remove="">
      Purge
    </button>

  </div>
  <table data-clean-media="">
    <thead>
      <tr>
        <th>
          ID
        </th>
        <th>
          Bild
        </th>
        <th>
          post_modified
        </th>
        <th>
          post_status
        </th>
        <th>
          post_title
        </th>
        <th>
          post_type
        </th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</section>
