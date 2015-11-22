<?php
header("Location: ".getHost());

function getHost() {
	$proto = (empty($_SERVER['HTTPS'])) ? "http://" : "https://";
	return $proto.$_SERVER['SERVER_NAME'];
}
?>