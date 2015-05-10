<?php
include "db.php";
include "header.html";
?>
<style>td {padding-right:10px;}
</style>
<h1>所有板块列表</h1>
<p>版权所有 &copy;2012 校团委政研室、网工办，仅供内部使用，禁止外传</p>
<table><tr><th>分区<th>英文名称<th>中文名称<th>版主<th>最后抓取<th>状态<th>帖子数</tr>
<?php
$rs = mysql_query("SELECT `name`,`title`,`moderator`,`section`,`status`,`crawl_time`,`crawl_interval`,(SELECT COUNT(*) FROM posts WHERE `board`=`name`) AS `post_count` FROM boards ORDER BY section");
while ($board = mysql_fetch_array($rs)) {
?>
<tr><td><?=$board['section'] ?><td><?=$board['name'] ?><td><?=$board['title'] ?><td><?=$board['moderator'] ?><td><?=date('Y-m-d h:i:s', $board['crawl_time']) ?>
<td><?=($board['status'] == 0 ? (time() - $board['crawl_time'] > $board['crawl_interval'] ? '等待' : '<font color="green">已更新</font>') : '<font color="red">抓取中</font>') ?>
<td><strong><?=$board['post_count'] ?></strong></tr>
<?php
}
?>
</table>
</body>
</html>
