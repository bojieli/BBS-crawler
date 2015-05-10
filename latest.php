<?php
include "db.php";
include "header.html";
?>
<style>table { font-size:14px;line-height:130%; } td { padding: 5px;}
.large {font-size: 20px; font-weight: bold; font-family:"Microsoft YaHei"; }
.einfo {height: auto !important; }
tr.light {background-color:#F4EEE9;} 
tr.dark {background-color:#E9F4EF;}
</style>
<h1>BBS新帖速览</h1>
<p>版权所有 &copy;2012 校团委政研室、网工办，仅供内部使用，禁止外传</p>
<?php
$num_per_page = 20;
$page = is_numeric($_GET['page']) ? $_GET['page'] : 0;
$query = "SELECT * FROM posts ";
$link = "latest.php?";
if (!empty($_GET['board'])) {
	$board = addslashes($_GET['board']);
	$query .= "WHERE `board` = '$board'";
	$link .= "board=$board&";
}
else if (isset($_GET['work'])) {
	if ($_GET['work'] == 1) {
		$query .= "WHERE `board` IN ('YoungComm', 'Clubs', 'StudentUnion', 'Volunteers')";
		$link .= "work=1&";
	}
	else if ($_GET['work'] == 2) {
		$query .= "WHERE `board` IN ('GradSchool', 'Library', 'OurGarden', 'Community', 'AssetsAndCampusMg')";
		$link .= "work=2&";
	}
	else if ($_GET['work'] == 3) {
		$query .= "WHERE `board` IN ('OurUSTC', 'USTCDevelopment', 'PresidentMbox')";
		$link .= "work=3&";
	}
	else if ($_GET['work'] == 4) {
		$query .= "WHERE `board` IN ('AbroadTest', 'AdvancedEdu', 'Psychology', 'Job', 'WorkLife', 'Football', 'Health')";
		$link .= "work=4&";
	}
	else {
		$query .= "WHERE `board` IN ('YoungComm', 'Clubs', 'StudentUnion', 'Volunteers', 'GradSchool', 'Library', 'OurGarden', 'Community', 'AssetsAndCampusMg', 'OurUSTC', 'USTCDevelopment', 'PresidentMbox', 'AbroadTest', 'AdvancedEdu', 'Psychology', 'Job', 'WorkLife', 'Football', 'Health')";
		$link .= "work=1&";
	}
}
$query .= " ORDER BY post_time DESC LIMIT ".$page * $num_per_page. ",". $num_per_page;
$num = 0;
$rs = mysql_query($query);
?>
<h2><a href="<?=$link?>page=<?=$page-1 ?>">上一页</a> (<?=($page-1)*$num_per_page ?>~<?=$page*$num_per_page-1 ?>) 当前页 (<?=$page*$num_per_page ?>~<?=($page+1)*$num_per_page-1 ?>) <a href="<?=$link?>page=<?=$page+1 ?>">下一页</a> (<?=($page+1)*$num_per_page ?>~<?=($page+2)*$num_per_page-1 ?>)</h2>
<table><tr><th>id<th>board<th>author<th>post_time<th>crawl_time</tr>
<?php
while ($post = mysql_fetch_array($rs)) { 
$num++;
?>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td colspan="5"><span class="large"><?php echo $post['title'] ?></span></tr>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td><?php echo $post['id'] ?><td><?php echo $post['board'] ?><td><strong><?php echo $post['author'] ?></strong><td>发帖：<?php echo date('Y-m-d H:i:s',$post['post_time']) ?><td>抓取：<?php echo date('Y-m-d H:i:s',$post['crawl_time']+3600*8) ?></tr>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td colspan="6"><div style="padding-left: 10px; padding-bottom: 15px;"><?php echo $post['content'] ?></div></tr>
<?php
}
?>
</table>
<h2><a href="<?=$link?>page=<?=$page-1 ?>">上一页</a> (<?=($page-1)*$num_per_page ?>~<?=$page*$num_per_page-1 ?>) 当前页 (<?=$page*$num_per_page ?>~<?=($page+1)*$num_per_page-1 ?>) <a href="<?=$link?>page=<?=$page+1 ?>">下一页</a> (<?=($page+1)*$num_per_page ?>~<?=($page+2)*$num_per_page-1 ?>)</h2>
</body>
</html>
