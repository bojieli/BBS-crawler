<?php
include "db.php";
include "header.html";
?>
<style>td {padding-right:10px;}
</style>
<h1>所有板块列表</h1>
<p>版权所有 &copy;2012 校团委政研室、网工办，仅供内部使用，禁止外传</p>
<h2><a href="latest.php" target="_blank">全站新帖</a> <a href="latest.php?work" target="_blank">所有工作版块新帖</a></h2>
<p>第一组：<a href="latest.php?work=1" target="_blank">YoungComm, Clubs, StudentUnion, Volunteers</a>
<p>第二组：<a href="latest.php?work=2" target="_blank">GradSchool, Library, OurGarden, Community, AssetsAndCampusMg</a>
<p>第三组：<a href="latest.php?work=3" target="_blank">OurUSTC, USTCDevelopment, PresidentMbox</a>
<p>第四组：<a href="latest.php?work=4" target="_blank">AbroadTest, AdvancedEdu, Psychology, Job, WorkLife, Football, Health</a></p>
<table><tr><th>分区<th>英文名称<th>中文名称<th>版主<th>最后抓取<th>状态</tr>
<?php
$rs = mysql_query("SELECT `name`,`title`,`moderator`,`section`,`status`,`crawl_time`,`crawl_interval` FROM boards ORDER BY section");
while ($board = mysql_fetch_array($rs)) {
?>
<tr><td><?=$board['section'] ?><td><a href="latest.php?board=<?=$board['name'] ?>" target="_blank"><?=$board['name'] ?></a><td><?=$board['title'] ?><td><?=$board['moderator'] ?><td><?=date('Y-m-d H:i:s', $board['crawl_time']+3600*8) ?>
<td><?=($board['status'] == 0 ? (time() - $board['crawl_time'] > $board['crawl_interval'] ? '等待' : '<font color="green">已更新</font>') : '<font color="red">抓取中</font>') ?>
</tr>
<?php
}
?>
</table>
</body>
</html>
