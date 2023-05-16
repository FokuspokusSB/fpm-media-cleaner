<?php

class MEDIA_CLEANER_CONFIG
{
  const TABLE_NAME = "fpm_media_cleaner";
  const OPTIONS_TABLE_NAME = "fpm_media_cleaner_options";

  const OPTIONS_KEYS = [
    "STATUS" => "status",
    "LAST_UPDATE" => "last_update",
    "COUNT" => "count",
    "SKIP_IDS" => "skip_ids",
  ];
}
