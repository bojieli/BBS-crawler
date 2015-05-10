<?php
$db['host'] = 'localhost';
$db['user'] = 'bbs-crawler';
$db['password'] = 'h9ref9r4';
$db['dbname'] = 'ustcbbs';

$status = mysql_connect($db['host'], $db['user'], $db['password']);
if (!$status)
	die('Database Connection Failure');
$status = mysql_select_db($db['dbname']);
if (!$status)
	die('Database Selection Failure');

unset($db);
?>
