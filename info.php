<?php
echo 'starting...';
include "db.php";
set_time_limit(0);
ob_end_flush();
while (1) {
	$rs = mysql_query("select count(*), count(*)/(max(crawl_time) - 1323703700) from posts where crawl_time > 1323703700");
	$list = mysql_fetch_array($rs, 0);
	echo '<tr><td>'.$list[0].'<td>'.$list[1].'</tr>';
	flush();
	sleep(1000);
}
