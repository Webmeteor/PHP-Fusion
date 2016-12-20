<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: php_bbcode_save.php
| Author: Wooya
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require "../../maincore.php";
require INCLUDES."class.httpdownload.php";

function unstripinput($text) {
   if (QUOTES_GPC) $text = stripslashes($text);
   $search = array("\n", "&amp;", "&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;");
   $replace = array("\r\n", "&", "\"", "'", "\\", '\"', "\'", "<", ">");
   $text = str_replace($search, $replace, $text);
   return $text;
}
?>
