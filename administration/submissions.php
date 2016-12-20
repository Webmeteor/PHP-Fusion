<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: submissions.php
| Author: Nick Jones (Digitanium)
| Co-Author: Daywalker
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../maincore.php";

if (!checkrights("SU") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

require_once THEMES."templates/admin_header_mce.php";
if ($settings['tinymce_enabled'] != 1) {
	require_once INCLUDES."html_buttons_include.php";
}
include LOCALE.LOCALESET."admin/submissions.php";

$news = ""; $articles = "";

if (!isset($_GET['action']) || $_GET['action'] == "1") {
	if (isset($_GET['delete']) && isnum($_GET['delete'])) {
		$result = dbquery("SELECT submit_type, submit_criteria FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['delete']."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			opentable($locale['400']);
			$result = dbquery("DELETE FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['delete']."'");
			echo "<br /><div style='text-align:center'>".$locale['401']."<br /><br />\n";
			echo "<a href='".FUSION_SELF.$aidlink."'>".$locale['402']."</a><br /><br />\n";
			echo "<a href='index.php".$aidlink."'>".$locale['403']."</a></div><br />\n";
			closetable();
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} else {
		$result = dbquery("SELECT submit_id, submit_criteria FROM ".DB_SUBMISSIONS." WHERE submit_type='n' ORDER BY submit_datestamp DESC");
		if (dbrows($result)) {
			while ($data = dbarray($result)) {
				$submit_criteria = unserialize($data['submit_criteria']);
				$news .= "<tr>\n<td class='tbl1'>".$submit_criteria['news_subject']."</td>\n";
				$news .= "<td align='right' width='1%' class='tbl1' style='white-space:nowrap'><span class='small'><a href='".FUSION_SELF.$aidlink."&amp;action=2&amp;t=n&amp;submit_id=".$data['submit_id']."'>".$locale['417']."</a></span> |\n";
				$news .= "<span class='small'><a href='".FUSION_SELF.$aidlink."&amp;delete=".$data['submit_id']."'>".$locale['418']."</a></span></td>\n</tr>\n";
			}
		} else {
			$news = "<tr>\n<td colspan='2' class='tbl1'>".$locale['415']."</td>\n</tr>\n";
		}
		$result = dbquery("SELECT submit_id, submit_criteria FROM ".DB_SUBMISSIONS." WHERE submit_type='a' ORDER BY submit_datestamp DESC");
		if (dbrows($result)) {
			while ($data = dbarray($result)) {
				$submit_criteria = unserialize($data['submit_criteria']);
				$articles .= "<tr>\n<td class='tbl1'>".$submit_criteria['article_subject']."</td>\n";
				$articles .= "<td align='right' width='1%' class='tbl1' style='white-space:nowrap'><span class='small'><a href='".FUSION_SELF.$aidlink."&amp;action=2&amp;t=a&amp;submit_id=".$data['submit_id']."'>".$locale['417']."</a></span> |\n";
				$articles .= "<span class='small'><a href='".FUSION_SELF.$aidlink."&amp;delete=".$data['submit_id']."'>".$locale['418']."</a></span></td>\n</tr>\n";
			}
		} else {
			$articles = "<tr>\n<td colspan='2' class='tbl1'>".$locale['416']."</td>\n</tr>\n";
		}
		opentable($locale['410']);
		echo "<table cellpadding='0' cellspacing='1' width='400' class='tbl-border center'>\n<tr>\n";
		echo "<td colspan='2' class='tbl2'><a id='news_submissions' name='news_submissions'></a>\n".$locale['412']."</td>\n";
		echo "</tr>\n".$news."<tr>\n";
		echo "<td colspan='2' class='tbl2'><a id='article_submissions' name='article_submissions'></a>\n".$locale['413']."</td>\n";
		echo "</tr>\n".$articles."<tr>\n</table>\n";
		closetable();
	}
}
if ((isset($_GET['action']) && $_GET['action'] == "2") && (isset($_GET['t']) && $_GET['t'] == "n")) {
	if (isset($_POST['publish']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
		$result = dbquery(
			"SELECT ts.*, tu.user_id, tu.user_name FROM ".DB_SUBMISSIONS." ts
			LEFT JOIN ".DB_USERS." tu ON ts.submit_user=tu.user_id
			WHERE submit_id='".$_GET['submit_id']."'"
		);
		if (dbrows($result)) {
			$data = dbarray($result);
			$news_subject = stripinput($_POST['news_subject']);
			$news_cat = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
			$news_snippet = addslash($_POST['news_snippet']);
			$news_body = addslash($_POST['news_body']);
			$news_breaks = ($_POST['news_breaks'] == "y") ? "y" : "n";
			$result = dbquery("INSERT INTO ".DB_NEWS." (news_subject, news_cat, news_news, news_extended, news_breaks, news_name, news_datestamp, news_start, news_end, news_visibility, news_reads, news_allow_comments, news_allow_ratings) VALUES ('$news_subject', '$news_cat', '$news_snippet', '$news_body', '$news_breaks', '".$data['user_id']."', '".time()."', '0', '0', '0', '0', '1', '1')");
			$result = dbquery("DELETE FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['submit_id']."'");
			opentable($locale['490']);
			echo "<br /><div style='text-align:center'>".$locale['491']."<br /><br />\n";
			echo "<a href='".FUSION_SELF.$aidlink."'>".$locale['402']."</a><br /><br />\n";
			echo "<a href='index.php".$aidlink."'>".$locale['403']."</a></div><br />\n";
			closetable();
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} else if (isset($_POST['delete']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
		opentable($locale['492']);
		$result = dbquery("DELETE FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['submit_id']."'");
		echo "<br /><div style='text-align:center'>".$locale['493']."<br /><br />\n";
		echo "<a href='".FUSION_SELF.$aidlink."'>".$locale['402']."</a><br /><br />\n";
		echo "<a href='index.php".$aidlink."'>".$locale['403']."</a></div><br />\n";
		closetable();
	} else {
		if ($settings['tinymce_enabled'] == 1) echo "<script type='text/javascript'>advanced();</script>\n";
		$result = dbquery(
			"SELECT ts.submit_criteria, tu.user_id, tu.user_name, tu.user_status
			FROM ".DB_SUBMISSIONS." ts
			LEFT JOIN ".DB_USERS." tu ON ts.submit_user=tu.user_id
			WHERE submit_id='".$_GET['submit_id']."'"
		);
		if (dbrows($result)) {
			$data = dbarray($result);
			$submit_criteria = unserialize($data['submit_criteria']);
			$news_subject = $submit_criteria['news_subject'];
			$news_cat = $submit_criteria['news_cat'];
			if (isset($submit_criteria['news_snippet'])) {
				$news_snippet = phpentities(stripslashes($submit_criteria['news_snippet']));
			} else {
				$news_snippet = "";
			}
			$news_body = phpentities(stripslashes($submit_criteria['news_body']));
			$news_breaks = "";
			$news_cat_opts = ""; $sel = "";
			$result2 = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
			if (dbrows($result2)) {
				while ($data2 = dbarray($result2)) {
					if (isset($news_cat)) $sel = ($news_cat == $data2['news_cat_id'] ? " selected='selected'" : "");
					$news_cat_opts .= "<option value='".$data2['news_cat_id']."'$sel>".$data2['news_cat_name']."</option>\n";
				}
			}
			add_to_title($locale['global_200'].$locale['503'].$locale['global_201'].$news_subject."?");
			if (isset($_POST['preview']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
				$news_subject = stripinput($_POST['news_subject']);
				$news_cat = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
				$news_snippet = stripslash($_POST['news_snippet']);
				$news_body = stripslash($_POST['news_body']);
				$breaks = (isset($_POST['line_breaks']) ? " checked='checked'" : "");
				opentable($news_subject);
				echo $locale['509']." ".(isset($_POST['line_breaks']) ? nl2br($news_snippet) : $news_snippet)."<br /><br />";
				echo $locale['508']." ".(isset($_POST['line_breaks']) ? nl2br($news_body) : $news_body);
				closetable();
			}
			opentable($locale['500']);
			echo "<form name='publish' method='post' action='".FUSION_SELF.$aidlink."&amp;sub=submissions&amp;action=2&amp;t=n&amp;submit_id=".$_GET['submit_id']."'>\n";
			echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
			echo "<td width='100' class='tbl'>".$locale['505']."</td>\n";
			echo "<td width='80%' class='tbl'><input type='text' name='news_subject' value='$news_subject' class='textbox' style='width: 250px' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='100' class='tbl'>".$locale['506']."</td>\n";
			echo "<td width='80%' class='tbl'><select name='news_cat' class='textbox'>\n";
			echo "<option value='0'>".$locale['507']."</option>\n".$news_cat_opts."</select></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td valign='top' width='100' class='tbl'>".$locale['509']."</td>\n";
			echo "<td width='80%' class='tbl'><textarea name='news_snippet' cols='60' rows='10' class='textbox' style='width:300px;'>".$news_snippet."</textarea></td>\n";
			echo "</tr>\n";
			if ($settings['tinymce_enabled'] != 1) {
				echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
				echo display_html("publish", "news_snippet", true, true, true);
				echo "</td>\n</tr>\n";
			}
			echo "<tr>\n";
			echo "<td valign='top' width='100' class='tbl'>".$locale['508']."</td>\n";
			echo "<td width='80%' class='tbl'><textarea name='news_body' cols='60' rows='10' class='textbox' style='width:300px;'>".$news_body."</textarea></td>\n";
			echo "</tr>\n";
			if ($settings['tinymce_enabled'] != 1) {
				echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
				echo display_html("publish", "news_body", true, true, true);
				echo "</td>\n</tr>\n";
			}
			echo "<tr>\n";
			echo "<td align='center' colspan='2' class='tbl1'><br />\n";
			echo $locale['501'].profile_link($data['user_id'], $data['user_name'], $data['user_status'])."<br /><br />\n";
			echo $locale['502']."<br />\n";
			echo "<input type='hidden' name='news_breaks' value='".$news_breaks."' />\n";
			echo "<input type='submit' name='preview' value='".$locale['510']."' class='button' />\n";
			echo "<input type='submit' name='publish' value='".$locale['503']."' class='button' />\n";
			echo "<input type='submit' name='delete' value='".$locale['504']."' class='button' />\n";
			echo "</td>\n</tr>\n</table>\n</form>\n";
			closetable();
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
}
if ((isset($_GET['action']) && $_GET['action'] == "2") && (isset($_GET['t']) && $_GET['t'] == "a")) {
	if (isset($_POST['publish']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
		$result = dbquery(
			"SELECT ts.submit_criteria, user_id
			FROM ".DB_SUBMISSIONS." ts
			LEFT JOIN ".DB_USERS." tu ON ts.submit_user=tu.user_id
			WHERE submit_id='".$_GET['submit_id']."'"
		);
		if (dbrows($result)) {
			$data = dbarray($result);
			$submit_criteria = unserialize($data['submit_criteria']);
			$article_cat = isnum($_POST['article_cat']) ? $_POST['article_cat'] : 0;
			$article_subject = stripinput($_POST['article_subject']);
			$article_snippet = addslash($_POST['article_snippet']);
			$article_body = addslash($_POST['article_body']);
			$article_breaks = ($_POST['article_breaks'] == "y") ? "y" : "n";
			$result = dbquery("INSERT INTO ".DB_ARTICLES." (article_cat, article_subject, article_snippet, article_article, article_breaks, article_name, article_datestamp, article_reads, article_allow_comments, article_allow_ratings) VALUES ('$article_cat', '$article_subject', '$article_snippet', '$article_body', '$article_breaks', '".$data['user_id']."', '".time()."', '0', '1', '1')");
			$result = dbquery("DELETE FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['submit_id']."'");
			opentable($locale['530']);
			echo "<br /><div style='text-align:center'>".$locale['531']."<br /><br />\n";
			echo "<a href='".FUSION_SELF.$aidlink."'>".$locale['402']."</a><br /><br />\n";
			echo "<a href='index.php".$aidlink."'>".$locale['403']."</a></div><br />\n";
			closetable();
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	} else if (isset($_POST['delete']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
		opentable($locale['532']);
		$result = dbquery("DELETE FROM ".DB_SUBMISSIONS." WHERE submit_id='".$_GET['submit_id']."'");
		echo "<br /><div style='text-align:center'>".$locale['533']."<br /><br />\n";
		echo "<a href='".FUSION_SELF.$aidlink."'>".$locale['402']."</a><br /><br />\n";
		echo "<a href='index.php".$aidlink."'>".$locale['403']."</a></div><br />\n";
		closetable();
	} else {
		if ($settings['tinymce_enabled'] == 1) {
			echo "<script type='text/javascript'>advanced();</script>\n";
		}
		$result = dbquery(
			"SELECT ts.submit_criteria, tu.user_id, tu.user_name, tu.user_status
			FROM ".DB_SUBMISSIONS." ts
			LEFT JOIN ".DB_USERS." tu ON ts.submit_user=tu.user_id
			WHERE submit_id='".$_GET['submit_id']."'"
		);
		if (dbrows($result)) {
			$data = dbarray($result);
			$submit_criteria = unserialize($data['submit_criteria']);
			$article_cat = $submit_criteria['article_cat'];
			$article_subject = $submit_criteria['article_subject'];
			$article_snippet = phpentities(stripslashes($submit_criteria['article_snippet']));
			$article_body = phpentities(stripslashes($submit_criteria['article_body']));
			$article_breaks = "";
			$result2 = dbquery("SELECT article_cat_id, article_cat_name FROM ".DB_ARTICLE_CATS." ORDER BY article_cat_name DESC");
			$article_cat_opts = ""; $sel = "";
			while ($data2 = dbarray($result2)) {
				if (isset($article_cat)) $sel = ($article_cat == $data2['article_cat_id'] ? " selected='selected'" : "");
				$article_cat_opts .= "<option value='".$data2['article_cat_id']."'$sel>".$data2['article_cat_name']."</option>\n";
			}
			add_to_title($locale['global_200'].$locale['543'].$locale['global_201'].$article_subject."?");
			if (isset($_POST['preview']) && (isset($_GET['submit_id']) && isnum($_GET['submit_id']))) {
				$article_cat = isnum($_POST['article_cat']) ? $_POST['article_cat'] : "0";
				$article_subject = stripinput($_POST['article_subject']);
				$article_snippet = stripslash($_POST['article_snippet']);
				$article_body = stripslash($_POST['article_body']);
				$breaks = (isset($_POST['line_breaks']) ? " checked='checked'" : "");
				opentable($article_subject);
				echo $locale['547']." ".(isset($_POST['line_breaks']) ? nl2br($article_snippet) : $article_snippet)."<br /><br />";
				echo $locale['548']." ".(isset($_POST['line_breaks']) ? nl2br($article_body) : $article_body);
				closetable();
			}
			opentable($locale['540']);
			echo "<form name='publish' method='post' action='".FUSION_SELF.$aidlink."&amp;sub=submissions&amp;action=2&amp;t=a&amp;submit_id=".$_GET['submit_id']."'>\n";
			echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
			echo "<td width='100' class='tbl'>".$locale['506']."</td>\n";
			echo "<td width='80%' class='tbl'><select name='article_cat' class='textbox'>\n".$article_cat_opts."</select></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td width='100' class='tbl'>".$locale['505']."</td>\n";
			echo "<td width='80%' class='tbl'><input type='text' name='article_subject' value='$article_subject' class='textbox' style='width: 250px' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td valign='top' width='100' class='tbl'>".$locale['547']."</td>\n";
			echo "<td width='80%' class='tbl'><textarea name='article_snippet' cols='60' rows='5' class='textbox' style='width:300px;'>".$article_snippet."</textarea></td>\n";
			echo "</tr>\n";
			if ($settings['tinymce_enabled'] != 1) {
				echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
				echo display_html("publish", "article_body", true, true, true);
				echo "</td>\n</tr>\n";
			}
			echo "<tr>\n";
			echo "<td valign='top' width='100' class='tbl'>".$locale['548']."</td>\n";
			echo "<td width='80%' class='tbl'><textarea name='article_body' cols='60' rows='10' class='textbox' style='width:300px;'>".$article_body."</textarea></td>\n";
			echo "</tr>\n";
			if ($settings['tinymce_enabled'] != 1) {
				echo "<tr>\n<td class='tbl'></td>\n<td class='tbl'>\n";
				echo display_html("publish", "article_body", true, true, true);
				echo "</td>\n</tr>\n";
			}
			echo "<tr>\n";
			echo "<td align='center' colspan='2' class='tbl1'><br />\n";
			echo $locale['541'].profile_link($data['user_id'], $data['user_name'], $data['user_status'])."<br /><br />\n";
			echo $locale['542']."<br />\n";
			echo "<input type='hidden' name='article_breaks' value='".$article_breaks."' />\n";
			echo "<input type='submit' name='preview' value='".$locale['549']."' class='button' />\n";
			echo "<input type='submit' name='publish' value='".$locale['543']."' class='button' />\n";
			echo "<input type='submit' name='delete' value='".$locale['544']."' class='button' />\n";
			echo "</td>\n</tr>\n</table>\n</form>\n";
			closetable();
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}
}

require_once THEMES."templates/footer.php";
?>