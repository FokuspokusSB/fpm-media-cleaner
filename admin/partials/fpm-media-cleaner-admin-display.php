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
      <th>
        Last Update Anzahl
      </th>
    </tr>
    <tr>
      <td>
        <span data-options-status="">
          --
        </span>
      </td>
      <td>
        <span data-options-last-update="">
          --
        </span>
      </td>
      <td>
        <span data-options-count="">
          --
        </span>
      </td>
    </tr>
  </table>
  <div class="controls">
    <div>
      <button type="button" data-fpm-media-cleaner-refresh="">
        Cache Tabelle füllen/aktualisieren
      </button>

      <button type="button" data-fpm-media-cleaner-remove="">
        Media Dateien löschen
      </button>
    </div>

    <span>
      Aktuelle Anzahl: 
      <span data-fpm-media-cleaner-count="">
      </span>
    </span>
  </div>

  <div class="progressbar">
    <progress data-fpm-media-progress="">
    </progress>

  </div>

  <table data-clean-media="">
    <thead>
      <tr>
        <th style="width: 50px;">
          ID
        </th>
        <th>
          Bild
        </th>
        <th>
          post_modified
        </th>
        <th style="width: 80px;">
          post_status
        </th>
        <th>
          post_title
        </th>
        <th style="width: 70px;">
          post_type
        </th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</section>
