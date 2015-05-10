<?php
include "db.php";
set_time_limit(0);
ignore_user_abort();
$bbs = "http://bbs.ustc.edu.cn/";
$rs = mysql_query("SELECT id FROM sections");
$boardqueue = array();

while ($section_id = mysql_fetch_array($rs)) {
	crawl_section($section_id[0], true, true);
}
foreach ($boardqueue as $board) {
	crawl_board($board, true);
}

function write_log($section, $board, $action) {
	$time = time();
	mysql_query("INSERT INTO `logs` (`section`,`board`,`action`,`time`) VALUES ('$section','$board','$action','$time')");
}

function crawl_section($section_id, $get_boards = false, $get_posts = false) {
	global $bbs;
	global $boardqueue;
	write_log($section_id, '', 'start');
	$path = "cgi/bbsboa?sec=$section_id";
	$file = file($bbs.$path);
	foreach ($file as $line) {
		$line = mb_convert_encoding($line, "utf-8", "gb2312");
		$count = preg_match('/<a href=bbsdoc\?board=(.+?)>/i', $line, $matches);
		if ($count == 0) continue;
		$board = addslashes($matches[1]);

		preg_match('@[○|●] (.+?)</a>@i', $line, $matches);
		$board_title = addslashes($matches[1]);
		preg_match('/<a href=bbsqry\?userid=(.+?)>/i', $line, $matches);
		$moderator = addslashes(!empty($matches[1]) ? $matches[1] : '');
		$curr_time = time();
		
		$oldboard = mysql_fetch_array(mysql_query("SELECT crawl_time, status, `crawl_interval` FROM boards WHERE `name` = '$board'"));
		if (!empty($oldboard)) {
			if ($curr_time - $oldboard['crawl_time'] < $board['crawl_interval'])
				continue;
			if ($oldboard['status']) {
				$boardqueue[] = $board;
				continue;
			}
			mysql_query("UPDATE boards SET `name` = '$board', `title` = '$board_title', `section` = '$section_id', `moderator` = '$moderator'");
		} else { // new board
			mysql_query("INSERT INTO boards SET `name` = '$board', `title` = '$board_title', `section` = '$section_id', `moderator` = '$moderator'");
		}	
		if ($get_boards) {
			crawl_board($board, $get_posts);
		}
	}
	write_log($section_id, '', 'finish');
}

function crawl_board($board_name, $get_posts = false) {
	$board = mysql_fetch_array(mysql_query("SELECT section, crawl_time, crawl_interval FROM boards WHERE `name` = '$board_name'"));
	if (empty($board))
		return;
	$curr_time = time();
	if ($curr_time - $board['crawl_time'] < $board['crawl_interval'])
		return; // we have crawled it within crawl crawl_interval (default 86400 seconds)
	write_log($board['section'], $board_name, 'start');
	mysql_query("UPDATE boards SET `status` = '1' WHERE `name` = '$board_name'");
	do_crawl_board($board_name, $get_posts);
	$curr_time = time();
	mysql_query("UPDATE boards SET `crawl_time` = '$curr_time', `status` = '0' WHERE `name` = '$board_name'");
	write_log($board['section'], $board_name, 'finish');
}

function do_crawl_board($board_name, $get_posts = false) {
	// max id of post on this page
	$posts_per_page = 20;
	$max = 0;
	$start = 0;
	do {
		$max = crawl_board_currpage($board_name, $start, $get_posts);
		$start = $max - $posts_per_page - $posts_per_page + 1;
	} while($start >= 0);
}

function crawl_board_currpage($board_name, $start = 0, $get_posts = false) {
	global $bbs;
	$max = 0;
	$crawled_num = 0;
	if ($start > 0)
		$path = $bbs."cgi/bbsdoc?board=$board_name&start=$start";
	else
		$path = $bbs."cgi/bbsdoc?board=$board_name";
	$file = file($path);
	foreach ($file as $line) {
		$line = mb_convert_encoding($line, "utf-8", "gb2312");
		$count = preg_match('@<a class="o_title" href="bbscon\?bn=(.+?)&amp;fn=(.+?)&amp;num=([0-9]+?)">(.+?)</a>@i', $line, $matches);
		if ($count == 0) continue;
		$bn = addslashes($matches[1]);
		$fn = addslashes($matches[2]);
		$num = addslashes($matches[3]);
		if ($num > $max)
			$max = $num;
		$title = addslashes($matches[4]);
		
		$count = mysql_result(mysql_query("SELECT COUNT(*) FROM posts WHERE `id` = '$fn' AND `board` = '$bn' AND `content` IS NOT NULL"), 0);
		if ($count > 0)
			continue; // crawled

		$url = $bbs."cgi/bbscon?bn=$bn&fn=$fn&num=$num";
		$file = file_get_contents($url);
		preg_match('@<div class="post_text">(.+?)</div>\n</td></tr>\n</table>@is', $file, $matches);
		$content = addslashes(mb_convert_encoding($matches[1], "utf-8", "gb2312"));
		if (empty($content))
			continue; // error

		preg_match('@发信人:&nbsp;(.+?)&nbsp;@i', $content, $matches);
		$author = $matches[1];
		preg_match('@\(([0-9]{4})年([0-9]{2})月([0-9]{2})日([0-9]{2}):([0-9]{2}):([0-9]{2})&nbsp;@i', $content, $matches);
		if (!empty($matches)) {
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$minute = $matches[5];
			$second = $matches[6];
			$post_time = gmmktime($hour, $minute, $second, $month, $day, $year);
		}
		else	$post_time = 0;

		// posts cannot be modified, REPLACE => INSERT
		$curr_time = time();
		$sql = "INSERT INTO posts SET `id` = '$fn', `board` = '$bn', `num` = '$num', `title` = '$title', `author` = '$author', `thread` = '$fn', `reply` = '$fn', `content` = '$content', `post_time` = '$post_time', `crawl_time` = '$curr_time'";
		mysql_query($sql);
		$crawled_num++;
	}
	if ($crawled_num > 0)
		return $max;
	else // no new posts, do not crawl further
		return 0;
}
?>
