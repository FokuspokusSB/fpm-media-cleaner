<?php
require_once "../../../../wp-load.php";
require_once "../../../../wp-includes/post.php";

global $wpdb;
$UPDATE = false;
$CONFIG = [
  "update" => false, // if true, than this script run db query for necessary data
  "tmp_dir" => "./tmp", // folder for tmp datas
  "file_name" => "not-linked-attachments", // file for tmp saving
];

/** DO NOT TOUCH */
if (
  sizeof(array_diff(scandir($CONFIG["tmp_dir"]), ["..", ".", ".gitkeep"])) == 0
) {
  $CONFIG["update"] = true;
}
$attachment_not_link_chunks = [];
if ($CONFIG["update"]) {
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

  // $results = $wpdb->get_results($SQL_NOT_DIRECT_LINKED, OBJECT);
  $attachments_not_direct_link = $wpdb->get_results(
    $SQL_NOT_DIRECT_LINKED,
    ARRAY_A
  );
  $attachment_not_link = [];
  foreach ($attachments_not_direct_link as $key => $attachment) {
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
    $post_content_result = $wpdb->get_results($SQL_SEARCH_IN_POST_CONTENT);
    $options_content_result = $wpdb->get_results($SQL_SEARCH_IN_OPTION_CONTENT);

    if (
      sizeof($post_content_result) == 0 &&
      sizeof($options_content_result) == 0
    ) {
      // is not in post_content
      $attachment["guid"] = str_replace(
        "http://kinedo.local/wp-content/uploads/",
        "",
        $attachment["guid"]
      );
      $attachment_not_link[] = $attachment;
    }
  }

  $attachment_not_link_chunks = array_chunk($attachment_not_link, 500);
  foreach ($attachment_not_link_chunks as $index => $chunk) {
    file_put_contents(
      $CONFIG["tmp_dir"] . "/" . $CONFIG["file_name"] . "-" . $index . ".json",
      json_encode($chunk)
    );
  }
}

$files = array_diff(scandir($CONFIG["tmp_dir"]), ["..", ".", ".gitkeep"]);
foreach ($files as $file) {
  $attachment_not_link_chunk = json_decode(
    file_get_contents($CONFIG["tmp_dir"] . "/" . $file)
  );
  foreach ($attachment_not_link_chunk as $attachment) {
    wp_delete_attachment($attachment->ID);
  }
  echo $file .
    ": Removed " .
    sizeof($attachment_not_link_chunk) .
    " attachments" .
    "<br>";
}
//
