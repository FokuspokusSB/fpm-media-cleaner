<?php

require_once plugin_dir_path(dirname(__FILE__)) .
  "includes/class-fpm-media-cleaner-config.php";

function buildTree(array $elements, $parentId = 0)
{
  $branch = [];

  foreach ($elements as $element) {
    if ((string) $element["parent"] === (string) $parentId) {
      $children = buildTree($elements, $element["id"]);
      if ($children) {
        $element["children"] = $children;
      }
      $branch[] = $element;
    }
  }

  return $branch;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://fokuspokus-media.de
 * @since      1.0.0
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fpm_Media_Cleaner
 * @subpackage Fpm_Media_Cleaner/admin
 * @author     Sören <sb@fokuspokus-media.de>
 */
class Fpm_Media_Cleaner_Admin
{
  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  private $db;
  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {
    global $wpdb;
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->db = $wpdb;

    add_action("admin_menu", [$this, "admin_menu"]);

    add_action("wp_ajax_media-clean-fill-cache", [$this, "action_fill_cache"]);
    add_action("wp_ajax_media-clean-remove", [$this, "action_remove"]);

    add_action("wp_ajax_media-clean-zip", [$this, "action_zip"]);
    add_action("wp_ajax_media-clean-remove-zip", [$this, "action_remove_zip"]);
    add_action("wp_ajax_media-clean-get-zip", [$this, "action_get_all_zips"]);

    add_action("wp_ajax_media-clean-get-cache", [$this, "action_get_cache"]);
    add_action("wp_ajax_media-clean-get-count", [$this, "action_get_count"]);

    add_action("wp_ajax_media-clean-set-skip", [$this, "action_set_skip"]);

    add_action("wp_ajax_media-clean-get-log", [$this, "action_get_log"]);
    add_action("wp_ajax_media-clean-reset-log", [$this, "action_reset_log"]);

    add_action("wp_ajax_media-clean-get-options", [
      $this,
      "action_get_options",
    ]);

    add_action("wp_ajax_media-clean-get-filebird-folders", [
      $this,
      "action_get_filebird_folders",
    ]);
    add_action("wp_ajax_media-clean-set-filebird-folders", [
      $this,
      "action_set_filebird_folders",
    ]);
  }

  private function _set_option($key, $value)
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $sql = "INSERT INTO {$table_name} (option_key,option_value) VALUES (%s,%s) ON DUPLICATE KEY UPDATE option_value = %s";
    $sql = $this->db->prepare($sql, $key, $value, $value);
    $this->db->query($sql);
  }

  private function _get_log()
  {
    $format_log = function ($v) {
      $v["insert_date"] = $v["insert_date"] . "+00:00";
      return $v;
    };

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::LOG_TABLE_NAME;
    $sql = "SELECT * FROM {$table_name} ORDER BY ID DESC";
    $sql = $this->db->prepare($sql);
    $results = $this->db->get_results($sql, ARRAY_A);
    return array_map($format_log, $results);
  }

  private function _set_log($status, $log, $count = 0)
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::LOG_TABLE_NAME;
    $this->db->insert($table_name, [
      "insert_date" => date("c"),
      "status" => $status,
      "count" => $count,
      "log" => json_encode($log),
    ]);
  }

  private function _reset_log()
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::LOG_TABLE_NAME;
    $this->db->query(
      '
			DELETE FROM `' .
        $table_name .
        "`"
    );
  }

  private function _get_options($key = false)
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::OPTIONS_TABLE_NAME;
    $sql = "SELECT * FROM {$table_name}";
    $sql = $this->db->prepare($sql);
    $results = $this->db->get_results($sql);

    if ($key) {
      foreach ($results as $value) {
        if ($key == $value->option_key) {
          return $value;
        }
      }
      return false;
    }

    return $results;
  }

  private function _get_skip_filebird_folders()
  {
    $option_filebird_folder_ids = $this->_get_options(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["EXTERNAL_PLUGIN_FILEBIRD_IDS"]
    );

    if ($option_filebird_folder_ids) {
      $option_filebird_folder_ids = json_decode(
        $option_filebird_folder_ids->option_value
      );
      if (!is_array($option_filebird_folder_ids)) {
        $option_filebird_folder_ids = false;
      }
    }
    return $option_filebird_folder_ids;
  }

  private function _get_param($key, $fallback_value)
  {
    $value = $fallback_value;
    if (
      array_key_exists($key, $_POST) &&
      $_POST[$key] &&
      !empty($_POST[$key])
    ) {
      $value = $_POST[$key];
    }
    return $value;
  }

  public function action_get_filebird_folders()
  {
    $fbv_table_name = $this->db->prefix . "fbv";
    $SQL =
      '
      SELECT *
      FROM `' .
      $fbv_table_name .
      '`
    ';
    $result = $this->db->get_results($SQL, ARRAY_A);

    $option_filebird_folder_ids = $this->_get_skip_filebird_folders();

    if (is_array($option_filebird_folder_ids)) {
      foreach ($option_filebird_folder_ids as $option_folder_id) {
        for ($i = 0; $i < sizeof($result); $i++) {
          if ($result[$i]["id"] === $option_folder_id) {
            $result[$i]["selected"] = true;
          }
        }
      }
    }

    $result = buildTree($result);
    echo json_encode($result);
    wp_die();
  }

  public function action_set_filebird_folders()
  {
    $skip_ids = $_POST["ids"];
    if (!$skip_ids) {
      wp_die();
      return;
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["EXTERNAL_PLUGIN_FILEBIRD_IDS"],
      json_encode($skip_ids)
    );
    echo json_encode(true);
    wp_die();
  }

  public function action_set_skip()
  {
    $skip_ids = $_POST["ids"];
    if (!$skip_ids) {
      wp_die();
      return;
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"],
      json_encode($skip_ids)
    );
    echo json_encode(true);
    wp_die();
  }

  public function action_fill_cache()
  {
    $this->_refill_cache_table();
    echo json_encode(true);
    wp_die();
  }

  public function action_remove_zip()
  {
    $zip = $_POST["zip"];
    if (!$zip) {
      wp_die();
      return;
    }

    $upload_dir = wp_upload_dir();
    $zip_path = $upload_dir["basedir"] . "/" . $zip;
    if (!file_exists($zip_path)) {
      wp_die();
      return;
    }

    unlink($zip_path);
    wp_die();
  }

  public function action_get_cache()
  {
    $page = $this->_get_param("page", 1);
    $limit = $this->_get_param("limit", 20);

    $offset = ($page - 1) * $limit;

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $post_table_name = $this->db->prefix . "posts";
    $SQL =
      '
      SELECT fpm.id, fpm.post_id, p.post_title, p.post_type, p.post_status, p.post_modified, p.guid
      FROM `' .
      $table_name .
      '` as fpm
      LEFT JOIN `' .
      $post_table_name .
      '` as p ON fpm.post_id = p.ID
      ORDER BY fpm.ID
      LIMIT ' .
      $offset .
      ", " .
      $limit;
    $result = $this->db->get_results($SQL, ARRAY_A);

    $COUNT_SQL = "SELECT COUNT(*) as total FROM `" . $table_name . "`;";
    $total = $this->db->get_row($COUNT_SQL, ARRAY_A);

    for ($i = 0; $i < sizeof($result); $i++) {
      if (wp_attachment_is_image($result[$i]["post_id"])) {
        $result[$i]["img"] = wp_get_attachment_image_src(
          $result[$i]["post_id"]
        );
      } else {
        $result[$i]["img"] = "";
      }
    }

    echo json_encode([
      "data" => $result,
      "total" => $total["total"],
    ]);

    wp_die();
  }

  public function action_get_options()
  {
    $options = $this->_get_options();

    $skip_ids_index = false;
    $filebird_skip_ids_index = false;
    foreach ($options as $i => $option) {
      if (
        $option->option_key == MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"]
      ) {
        $skip_ids_index = $i;
      }
      if (
        $option->option_key ==
        MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["EXTERNAL_PLUGIN_FILEBIRD_IDS"]
      ) {
        $filebird_skip_ids_index = $i;
      }
    }

    // get skip_ids media infos
    if ($skip_ids_index) {
      $options[$skip_ids_index]->option_value = json_decode(
        $options[$skip_ids_index]->option_value
      );
      if (is_array($options[$skip_ids_index]->option_value)) {
        for (
          $i = 0;
          $i < sizeof($options[$skip_ids_index]->option_value);
          $i++
        ) {
          $attachment = wp_get_attachment_image_src(
            $options[$skip_ids_index]->option_value[$i]
          );
          if ($attachment) {
            $attachment = $attachment[0];
          }
          $options[$skip_ids_index]->option_value[$i] = [
            "src" => $attachment,
            "id" => $options[$skip_ids_index]->option_value[$i],
          ];
        }
      }
    }

    if ($filebird_skip_ids_index) {
      $options[$filebird_skip_ids_index]->option_value = json_decode(
        $options[$filebird_skip_ids_index]->option_value
      );
      if (is_array($options[$filebird_skip_ids_index]->option_value)) {
        $filebird_sql =
          '
          SELECT id, name, count(folder_id) as count
          FROM `' .
          $this->db->prefix .
          'fbv` as fb
          INNER JOIN `' .
          $this->db->prefix .
          'fbv_attachment_folder` as f on fb.id = f.folder_id
          WHERE fb.id in (' .
          join(",", $options[$filebird_skip_ids_index]->option_value) .
          ')
          GROUP BY folder_id
        ';
        $options[$filebird_skip_ids_index]->option_value = $this->db->get_results($filebird_sql, ARRAY_A);
      }
    }

    echo json_encode($options);
    wp_die();
  }

  public function action_remove()
  {
    $this->_media_remove();
    echo "true";
    wp_die();
  }

  public function action_zip()
  {
    $zip_path = $this->_media_zip();
    echo json_encode($zip_path);
    wp_die();
  }

  public function action_get_all_zips()
  {
    $result = $this->_get_all_media_zips();
    echo json_encode($result);
    wp_die();
  }

  public function action_get_count()
  {
    $result = [
      "count" => $this->_get_count(),
    ];
    echo json_encode($result);
    wp_die();
  }

  public function action_get_log()
  {
    $result = $this->_get_log();
    echo json_encode($result);
    wp_die();
  }
  public function action_reset_log()
  {
    $result = $this->_reset_log();
    echo "true";
    wp_die();
  }

  private function _get_file_path($post_id)
  {
    $file_path = "";
    if (wp_attachment_is_image($post_id)) {
      $file_path = wp_get_original_image_path($post_id);
    } else {
      $file_path = get_attached_file($post_id);
    }
    return $file_path;
  }

  private function _media_zip()
  {
    $upload_dir = wp_upload_dir();

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    $SQL =
      '
      SELECT id, post_id
      FROM `' .
      $table_name .
      '`
    ';
    $result = $this->db->get_results($SQL, ARRAY_A);

    if (sizeof($result) <= 0) {
      return;
    }

    $zip = new ZipArchive();
    $zip_filename = "media-cleaner-" . time() . ".zip";
    $zip_path = $upload_dir["basedir"] . "/" . $zip_filename;
    $zip_url = $upload_dir["baseurl"] . "/" . $zip_filename;

    if ($zip->open($zip_path, ZipArchive::CREATE) !== true) {
      error_log(print_r("cannot open <$zip_path>\n", true));
      return;
    }

    foreach ($result as $row) {
      $file_path = $this->_get_file_path($row["post_id"]);

      if (!file_exists($file_path)) {
        error_log(
          "attachment with id '" .
            $row["post_id"] .
            "' not exists: [" .
            $file_path .
            "]"
        );
        continue;
      }

      $zip_file_name = basename($file_path);
      $zip->addFile($file_path, $zip_file_name);
    }
    $zip->close();
    return $zip_url;
  }

  private function _get_all_media_zips()
  {
    $upload_dir = wp_upload_dir();

    $file_filter = function ($val) {
      $file_start = "media-cleaner-";
      return str_starts_with($val, $file_start);
    };
    $file_map = function ($val) {
      $upload_dir = wp_upload_dir();
      $filename = basename($val);
      $split_file_name = explode("-", str_replace(".zip", "", $filename));
      $file_date = $split_file_name[array_key_last($split_file_name)];
      return [
        "filename" => $filename,
        "url" => $upload_dir["baseurl"] . "/" . $filename,
        "date" => $file_date,
      ];
    };

    $export_zip_root_dir = $upload_dir["basedir"];
    $files = scandir($export_zip_root_dir);
    $media_cleaner_exports = array_values(array_filter($files, $file_filter));
    return array_map($file_map, $media_cleaner_exports);
  }

  private function _media_remove()
  {
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["process-remove"]
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;
    // $post_table_name = $this->db->prefix . "posts";
    $SQL =
      '
      SELECT id, post_id
      FROM `' .
      $table_name .
      '`
    ';
    $result = $this->db->get_results($SQL, ARRAY_A);
    $log = [];
    foreach ($result as $row) {
      $log[] = [
        "filepath" => $this->_get_file_path($row["post_id"]),
        "post_id" => $row["post_id"],
      ];

      wp_delete_attachment($row["post_id"]);
      $this->db->delete($table_name, ["id" => $row["id"]]);
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["finish-remove"]
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"],
      $this->_get_count()
    );

    $this->_set_log(
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["finish-remove"],
      $log,
      sizeof($result)
    );
  }

  private function _get_count()
  {
    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;

    $SQL =
      '
      SELECT COUNT(*) as count
      FROM `' .
      $table_name .
      '`
    ';
    $count = $this->db->get_row($SQL, ARRAY_A);
    return $count["count"] ?: "0";
  }

  private function _refill_cache_table()
  {
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["process-cache"]
    );
    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );
    $this->_set_option(MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"], "0");

    $skip_ids = $this->_get_options(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["SKIP_IDS"]
    );

    if ($skip_ids) {
      $skip_ids = json_decode($skip_ids->option_value);
      if (!is_array($skip_ids)) {
        $skip_ids = false;
      }
    }

    $skip_folder_ids = $this->_get_skip_filebird_folders();
    if (is_array($skip_folder_ids)) {
      $filebird_sql =
        '
        SELECT attachment_id
        FROM `' .
        $this->db->prefix .
        'fbv_attachment_folder`
        WHERE folder_id in (' .
        join(",", $skip_folder_ids) .
        ");";
      $filebird_skipped_ids = $this->db->get_results($filebird_sql, ARRAY_A);
      for ($i = 0; $i < sizeof($filebird_skipped_ids); $i++) {
        $filebird_skipped_ids[$i] = $filebird_skipped_ids[$i]["attachment_id"];
      }
      if (
        !is_array($skip_ids) &&
        is_array($filebird_skipped_ids) &&
        sizeof($filebird_skipped_ids) > 0
      ) {
        $skip_ids = [];
      }
      if (
        is_array($filebird_skipped_ids) &&
        sizeof($filebird_skipped_ids) > 0
      ) {
        $skip_ids = array_merge($skip_ids, $filebird_skipped_ids);
      }
    }

    $table_name = $this->db->prefix . MEDIA_CLEANER_CONFIG::TABLE_NAME;

    $TRUNCATE_SQL =
      '
      TRUNCATE TABLE `' .
      $table_name .
      '`
    ';
    $this->db->query($TRUNCATE_SQL);

    $SQL_NOT_DIRECT_LINKED = "
    SELECT p.ID, p.guid
    FROM wp_posts as p
    WHERE p.post_type = 'attachment'
    AND p.ID not in (
      SELECT p.ID
      FROM wp_posts as p
      INNER JOIN wp_postmeta as pm
      ON p.ID = pm.meta_value
      WHERE p.post_type = 'attachment'
    )
    AND p.ID not in (
      SELECT p.ID
      FROM wp_posts as p
      INNER JOIN wp_options as po
      ON p.ID = po.option_value
      WHERE p.post_type = 'attachment'
    )";

    $attachments_not_direct_link = $this->db->get_results(
      $SQL_NOT_DIRECT_LINKED,
      ARRAY_A
    );
    // $attachment_not_link = [];
    // $place_holders = [];

    $log = [];
    foreach ($attachments_not_direct_link as $key => $attachment) {
      if ($skip_ids && in_array($attachment["ID"], $skip_ids)) {
        continue;
      }
      $SQL_SEARCH_IN_POST_CONTENT = "
        SELECT post_content 
        FROM wp_posts 
        WHERE option_value like '%{$attachment["ID"]}%'
      ";
      $SQL_SEARCH_IN_OPTION_CONTENT = " 
        SELECT post_content 
        FROM wp_posts
        WHERE post_content like '%{$attachment["ID"]}%'
      ";
      $SQL_SEARCH_IN_POST_META = " 
        SELECT pm.meta_value as meta_value
        FROM wp_postmeta as pm, wp_posts as p
        WHERE pm.post_id = p.ID
        AND meta_value like '%{$attachment["ID"]}%' 
        AND p.post_type != 'revision'
      ";
      $post_content_result = $this->db->get_results(
        $SQL_SEARCH_IN_POST_CONTENT
      );
      $options_content_result = $this->db->get_results(
        $SQL_SEARCH_IN_OPTION_CONTENT
      );
      $post_meta_content_result = $this->db->get_results(
        $SQL_SEARCH_IN_POST_META,
        ARRAY_A
      );

      if (sizeof($post_meta_content_result) > 0) {
        $tmp = [];
        foreach ($post_meta_content_result as $row) {
          if (
            $row['meta_value'] != $attachment["ID"] &&
            strpos($row['meta_value'], "\"" . $attachment["ID"] . "\"") !== false
          ) {
            $tmp[] = $row;
          }
        }
        $post_meta_content_result = $tmp;
      }

      if (
        sizeof($post_content_result) == 0 &&
        sizeof($options_content_result) == 0 &&
        sizeof($post_meta_content_result) == 0
      ) {
        $this->db->insert($table_name, [
          "post_id" => $attachment["ID"],
          "insert_date" => date("Y-m-d H:i:s"),
        ]);
        $log[] = [
          "post_id" => $attachment["ID"],
          "filepath" => $this->_get_file_path($attachment["ID"]),
        ];
      }
    }

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["STATUS"],
      MEDIA_CLEANER_CONFIG::STATUS_VALUES["finish-cache"]
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["LAST_UPDATE"],
      date("c")
    );

    $this->_set_option(
      MEDIA_CLEANER_CONFIG::OPTIONS_KEYS["COUNT"],
      $this->_get_count()
    );
  }

  public function add_plugin_links($links)
  {
    // $url = esc_url(
    //   add_query_arg(
    //     "page",
    //     "nelio-content-settings",
    //     get_admin_url() . "admin.php"
    //   )
    // );
    // // Create the link.
    // $settings_link = "<a href='$url'>" . __("Settings") . "</a>";
    // // Adds the link to the end of the array.
    // array_push($links, $settings_link);
    return $links;
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Fpm_Media_Cleaner_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Fpm_Media_Cleaner_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_style(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "css/fpm-media-cleaner-admin.css",
      [],
      $this->version,
      "all"
    );
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Fpm_Media_Cleaner_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Fpm_Media_Cleaner_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */
    wp_enqueue_media();

    wp_enqueue_script(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "js/fpm-media-cleaner-admin.js",
      ["jquery", "jquery-ui-dialog"],
      $this->version,
      false
    );

    wp_enqueue_style("wp-jquery-ui-dialog");
  }

  public function admin_menu()
  {
    add_menu_page(
      "Media Cleaner",
      "Media Cleaner",
      "publish_pages",
      "fpm-media-cleaner",
      [$this, "admin_page"],
      "data:image/svg+xml;base64," .
        base64_encode(
          file_get_contents(FPM_MEDIA_CLEANER_ROOT_DIR . "img/icon.svg")
        )
    );
  }

  public function admin_page()
  {
    $activate_plugins = (array) get_option("active_plugins", []);

    $external_plugins = [
      "filebird" => false,
    ];
    foreach ($activate_plugins as $value) {
      if (str_contains($value, "filebird")) {
        $external_plugins["filebird"] = true;
      }
    }

    require_once plugin_dir_path(dirname(__FILE__)) .
      "admin/partials/fpm-media-cleaner-admin-display.php";
  }
}
