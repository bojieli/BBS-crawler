<?php
include "db.php";
include "header.html";
?>
<h1>BBS爬虫状态</h1>
<p>版权所有 &copy;2012 校团委政研室、网工办，仅供内部使用，禁止外传</p>
<?php
$total_boards = mysql_result(mysql_query("SELECT COUNT(*) FROM boards"),0);
$total_posts = mysql_result(mysql_query("SELECT COUNT(*) FROM posts"),0);
$time = time();
$speed_now = mysql_result(mysql_query("select count(*)/3.5 from posts where `crawl_time` > $time - 4"),0);
$speed_minute = mysql_result(mysql_query("select count(*)/60 from posts where `crawl_time` > $time - 60"),0);
$speed_hour = mysql_result(mysql_query("select count(*)/3600 from posts where `crawl_time` > $time - 3600"),0);
$time = time();
?>
<p>当前时间：<?=$time ?> (<?=date("Y-m-d H:i:s", $time); ?>)
<p>总板块数：<?=$total_boards ?>
<p>总帖子数：<?=$total_posts ?>
<p>爬虫当前速度：<?=$speed_now ?> 帖子/秒
<p>爬虫1分钟速度：<?=$speed_minute ?> 帖子/秒
<p>爬虫1小时速度：<?=$speed_hour ?> 帖子/秒（如果启动不足1小时就不准了）
<h2>爬虫状态</h2>
<table><tr><th>name<th>title<th>moderator<th>section</tr>
<?php
$rs = mysql_query("SELECT * FROM boards WHERE `status`=1");
while ($log = mysql_fetch_array($rs)) { ?>
<tr><td><?=$log['name'] ?><td><?=$log['title']?><td><?=$log['moderator']?><td><?=$section ?></tr>
<?php } ?>
</table>

<h2>最新帖子</h2>
<table><tr><th>board<th>author<th>title<th>post_time</tr>
<?php
$rs = mysql_query("SELECT board,title,author,post_time FROM posts ORDER BY crawl_time DESC LIMIT 0,10");
while ($log = mysql_fetch_array($rs)) { ?>
<tr><td><?=$log['board'] ?><td><?=$log['title']?><td><?=$log['author']?><td><?=date('Y-m-d H:i:s',$log['post_time'])?></tr>
<?php }
?>
</table>

<h2>爬虫最近活动</h2>
<table><tr><th>section<th>board<th>action<th>time</tr>
<?php
$rs = mysql_query("SELECT * FROM logs ORDER BY time DESC LIMIT 0,10");
while ($log = mysql_fetch_array($rs)) { ?>
<tr><td><?=$log['section'] ?><td><?=$log['board']?><td><?=$log['action']?><td><?=date('Y-m-d H:i:s',$log['time'])?></tr>
<?php }
?>
</table>


