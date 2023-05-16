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
  <table>
    <thead>
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
          Skip Bilder
        </th>
        <th style="width:30px;">
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

  <div class="controls">
    <div>
      <button type="button" data-add-skip-images="">
        Skip Bilder auswählen
      </button>

      <button type="button" data-fpm-media-cleaner-refresh="">
        Cache Tabelle füllen/aktualisieren
      </button>

      <button type="button" data-fpm-media-cleaner-remove="" class="warn">
        Media Dateien löschen
      </button>
    </div>

    <span>
      Aktuelle Anzahl: 
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
          ID
        </th>
        <th style="width: 70px;">
          Bild
        </th>
        <th style="width: auto">
          Titel
        </th>
        <th style="width: 85px;">
          aktualisiert am
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
</section>
