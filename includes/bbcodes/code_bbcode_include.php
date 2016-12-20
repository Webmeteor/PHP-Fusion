<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2009 Nick Jones
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: code_bbcode_include.php
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
if (!defined("IN_FUSION")) { die("Access Denied"); }

$code_count = substr_count($text, "[code]");
for ($i=0; $i < $code_count; $i++) {
    $code_save = "";
	$text = preg_replace("#\[code\](.*?)\[/code\]#sie", "'<div class=\'code_bbcode\'><div class=\'tbl-border tbl2\' style=\'width:400px\'>".$code_save."<strong>".$locale['bb_code_code']."</strong></div><div class=\'tbl-border tbl1\' style=\'width:400px;white-space:nowrap;overflow:auto\'><code style=\'white-space:nowrap\'>'.formatcode('\\1').'<br /><br /><br /></code></div></div>'", $text, 1);
}
?>
