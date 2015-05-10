<?php
include "db.php";
$sections = array ('本站系统', '部门组织', '信息论坛', '我们的家', '电脑技术', '学术科学', '文化艺术', '休闲感性', '体育健身', '瀚海特区');
foreach($sections as $index => $section) {
	mysql_query("INSERT INTO sections SET `id` = '$index', `title` = '$section'");
}
