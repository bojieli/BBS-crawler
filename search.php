<?php
include "db.php";
set_time_limit(0);
include "header.html";
?>
<style>table { font-size:14px;line-height:130%; } td { padding: 5px;}
.large {font-size: 20px; font-weight: bold; font-family:"Microsoft YaHei"; }
.einfo {height: auto !important; }
</style>
<?php /*<h1>关键词搜索</h1>
<h2>由于数据库未经优化，可能需要数十秒，请耐心等待。</h2>*/ ?>
<h1>查询作者</h1>
<p>版权所有 &copy;2012 校团委政研室、网工办，仅供内部使用，禁止外传</p>
<form action="search.php" method="post">
<?php /*以下查询关键词任选其一。
<br>查询全部：<input type="text" name="query" />（模糊）
<br>查询标题：<input type="text" name="title" />（模糊）
<br>查询作者：<input type="text" name="author" />*/ ?>
<input style="height: 32px; font-size: 20px; " type="text" name="author" />&nbsp;&nbsp;<input style="height: 32px; font-size: 20px;" type="submit" value="搜索" />
</form>

<?php
$query = "SELECT * FROM posts WHERE ";
if (!empty($_REQUEST['author'])) {
	$content = addslashes($_REQUEST['author']);
	$query .= "`author` = '$content' AND";
	$flag = true;
}

if (!empty($_REQUEST['title'])) {
	$content = addslashes($_REQUEST['title']);
	$query .= "`title` = '$content' AND";
	$flag = true;
}
else if (!empty($_REQUEST['query'])) {
	$content = addslashes($_REQUEST['query']);
	$query .= "(`title` LIKE '%$content%' OR `content` LIKE '%$content%') AND";
	$flag = true;
}
$query .= " 1=1 ORDER BY post_time DESC";
if (empty($flag))
	exit();
?>
<p>正在搜索 <?php echo $query ?>
<h2>搜索结果：</h2>
<style>tr.light {background-color:#F4EEE9;} 
tr.dark {background-color:#E9F4EF;}
</style>
<table><tr><th>id<th>board<th>author<th>post_time<th>crawl_time</tr>
<?php
$num = 0;
$rs = mysql_query($query);
while ($post = mysql_fetch_array($rs)) { 
$num++;
?>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td colspan="5"><span class="large"><?php echo $post['title'] ?></span></tr>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td><?php echo $post['id'] ?><td><?php echo $post['board'] ?><td><strong><?php echo $post['author'] ?></strong><td>发帖：<?php echo date('Y-m-d H:i:s',$post['post_time']) ?><td>抓取：<?php echo date('Y-m-d H:i:s',$post['crawl_time']+3600*8) ?></tr>
<tr class="<?php echo $num%2 ? 'light' : 'dark' ?>"><td colspan="6"><div style="border-bottom: 2px dashed #333; padding-left: 10px; padding-bottom: 10px; margin-bottom: 10px;"><?php echo $post['content'] ?></div></tr>
<?php
}
?>
</table>
</body>
</html>
