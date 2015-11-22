<?php
session_start();
session_destroy();
header('WWW-Authenticate: Basic realm="logout"');
header("Location: ".getHost());

function getHost() {
	$proto = (empty($_SERVER['HTTPS'])) ? "http://" : "https://";
	return $proto."logout@".$_SERVER['SERVER_NAME']."/r.php";
}
?>