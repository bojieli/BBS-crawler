<?php
{
	$bn = 'AI';
	$fn = 'M4ED1F3EB';
	$num = '6706';

		$bbs = "http://bbs.ustc.edu.cn/";
		include "db.php";
		$count = mysql_result(mysql_query("SELECT COUNT(*) FROM posts WHERE `id` = '$fn' AND `board` = '$bn' AND `content` IS NOT NULL"), 0);
		if ($count > 0)
			continue; // crawled

		$url = $bbs."cgi/bbscon?bn=$bn&fn=$fn&num=$num";
		$file = file_get_contents($url);
		preg_match('@<div class="post_text">(.+?)</div>\n</td></tr>\n</table>@is', $file, $matches);
		$content = addslashes(mb_convert_encoding($matches[1], 'utf-8', 'gb2312'));
		if (empty($content))
			continue; // error

		preg_match('@发信人:&nbsp;(.+?)&nbsp;@i', $content, $matches);
		$author = $matches[1];
		preg_match('@瀚海星云&nbsp;\(([0-9]{4})年([0-9]{2})月([0-9]{2})日([0-9]{2}):([0-9]{2}):([0-9]{2})&nbsp;@i', $content, $matches);
		$year = $matches[1];
		$month = $matches[2];
		$day = $matches[3];
		$hour = $matches[4];
		$minute = $matches[5];
		$second = $matches[6];
		$post_time = gmmktime($hour, $minute, $second, $month, $day, $year);

		$curr_time = time();
		$sql = "REPLACE INTO posts SET `id` = '$fn', `board` = '$bn', `num` = '$num', `title` = '$title', `author` = '$author', `thread` = '$fn', `reply` = '$fn', `content` = '$content', `post_time` = '$post_time', `crawl_time` = '$curr_time'";
		mysql_query($sql);
		echo $sql;
}
exit();
include "db.php";
$bbs = "http://bbs.ustc.edu.cn/";
crawl_board('ESSclub');
function crawl_board($board_name) {
	$min = crawl_board_currpage($board_name, 0);
	$posts_per_page = 20;
	while ($min > 1) {
		$min = ($min > 20) ? $min - 20 : 1;
		crawl_board_currpage($board_name, $min);
	}
}
function crawl_board_currpage($board_name, $start = 0) {
	global $bbs;
	$min = 10000000; // maxint
	if ($start > 0)
		$path = $bbs."cgi/bbsdoc?board=$board_name&start=$start";
	else
		$path = $bbs."cgi/bbsdoc?board=$board_name";
	$file = file($path);
	foreach ($file as $line) {
		$line = mb_convert_encoding($line, "utf-8", "gb2312");
		$count = preg_match('@<a class="o_title" href="bbscon\?bn=(.+?)&amp;fn=(.+?)&amp;num=([0-9]+?)">(.+?)</a>@i', $line, $matches);
		if ($count == 0) continue;
		$bn = $matches[1];
		$fn = $matches[2];
		$num = $matches[3];
		if ($num < $min)
			$min = $num;
		$title = $matches[4];
		$sql = "REPLACE INTO posts SET `id` = '$fn', `board` = '$bn', `num` = '$num', `title` = '$title', `author` = '', `thread` = '$fn', `reply` = '$fn'";
		mysql_query($sql);
		echo "$sql <br>\n";
	}
	return $min;
}
?>
