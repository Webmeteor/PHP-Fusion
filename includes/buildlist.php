<?php
/*---------------------------------------------------+
| buildlist.php - iLister enginge.
+----------------------------------------------------+
| Copyright (C) 2005 Johs Lind
| http://www.geltzer.dk/
| Inspired by: PHP-Fusion 6 coding
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

$image_files = array();

// images ------------------------
$temp = opendir(IMAGES);
while ($file = readdir($temp)) {
	if (!in_array($file, array(".", "..", "/", "index.php", "imagelist.js")) && !is_dir(IMAGES.$file)) {
		$image_files[] = "['".$locale['422'].": ".$file."','".$settings['siteurl']."images/".$file."'], ";
	}
}
closedir($temp);

// articles ---------------
$temp = opendir(IMAGES_A);
while ($file = readdir($temp)) {
	if (!in_array($file, array(".", "..", "/", "index.php"))) {
		$image_files[] = "['".$locale['423'].": ".$file."','".$settings['siteurl']."images/articles/".$file."'], ";
	}
}
closedir($temp);
	
// news -------------------
$temp = opendir(IMAGES_N);
while ($file = readdir($temp)) {
	if (!in_array($file, array(".", "..", "/", "index.php")) && !is_dir(IMAGES_N.$file)) {
		$image_files[] = "['".$locale['424'].": ".$file."','".$settings['siteurl']."images/news/".$file."'], ";
	}
}
closedir($temp);
	
// news cats -------------------
$temp = opendir(IMAGES_NC);
while ($file = readdir($temp)) {
	if (!in_array($file, array(".", "..", "/", "index.php")) && !is_dir(IMAGES_NC.$file)) {
		$image_files[] = "['".$locale['427'].": ".$file."','".$settings['siteurl']."images/news_cats/".$file."'], ";
	}
}
closedir($temp);

sort($image_files);

// compile list -----------------
if (isset($image_files)) {
	$indhold = "var tinyMCEImageList = new Array(";
	for ($i = 0; $i < count($image_files); $i++){
		$indhold .= $image_files[$i];
	}
	$lang = strlen($indhold) - 2;
	$indhold = substr($indhold, 0, $lang);
	$indhold = $indhold.");";
	$fp = fopen(IMAGES."imagelist.js", "w");
	fwrite($fp, $indhold);
	fclose($fp);
}
?>