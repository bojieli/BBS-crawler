<?php
include "db.php";
set_time_limit(0);
include "header.html";
?>
<style>table { font-size:14px;line-height:130%; } td { padding: 5px;}
.large {font-size: 20px; font-weight: bold; font-family:"Microsoft YaHei"; }</style>
<h1>关键词搜索</h1>
<h2>由于数据库未经优化，可能需要数十秒，请耐心等待。</h2>
<form action="search.php" method="post">
以下查询关键词任选其一。
<br>查询全部：<input type="text" name="query" />（模糊）
<br>查询标题：<input type="text" name="title" />（模糊）
<br>查询作者：<input type="text" name="author" />
<br><input type="submit" value="搜索" />
</form>

<?php
$query = "SELECT * FROM posts WHERE ";
/*if (!empty($_REQUEST['author'])) {
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
	if ($content == 'wushulian')*/
		$content = '武书连';
	$query .= "(`title` LIKE '%$content%' OR `content` LIKE '%$content%') AND";
	$flag = true;
/*}*/
$query .= " 1=1";
if (empty($flag))
	exit();
?>
<p>正在搜索 <?php echo $query ?>
<h2>搜索结果：</h2>
<table><tr><th>id<th>board<th>author<th>post_time<th>crawl_time</tr>
<?php
$rs = mysql_query($query);
while ($post = mysql_fetch_array($rs)) { ?>
<tr><td colspan="5"><span class="large"><?php echo $post['title'] ?></span></tr>
<tr><td><?php echo $post['id'] ?><td><?php echo $post['board'] ?><td><strong><?php echo $post['author'] ?></strong><td><?php echo date('Y-m-d h:i:s',$post['post_time']) ?><td><?php echo date('Y-m-d h:i:s',$post['crawl_time']) ?></tr>
<tr><td colspan="6"><?php echo $post['content'] ?></tr>
<?php
}
?>
</table>
</body>
</html>
