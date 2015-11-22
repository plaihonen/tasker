<?php
session_start();
/**
 *	Class for authenticating the site
 *	
 *	by Petri Laihonen 26.01.2014
 *	
 */

$provideResponse = false;
$respArry = array();
$salt = "æøæ|asdrSDRAG12556AWQ899REG1WqerG#¤%#%&¤#YWRGHåäG?Rq+oew£#»qwer";
$server = getHost();
debugLog("Server: ".$server);

// sanitize some
$_POST = sanitize($_POST);

// Database credentials
require_once("dbCredentials.php");
debugLog("Required includes are now with us");

if (!$dbConn = mysqli_connect('localhost', $dbUser, $dbPass, $database)) {
	$response = array('status' 	=> 'fail', 
					  'message' => 'database connect error.'
					 );
	debugLog('Database Connection Failed. Error:'.mysqli_error());
	echo json_encode($response);
	exit;
}

if (!$_SESSION['userId']) {
	
	if ((!isset( $_SERVER['PHP_AUTH_USER'] )) || (!isset($_SERVER['PHP_AUTH_PW']))) {

		header('HTTP/1.0 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="Management Access"');
		$displayLogin = 1;
		// header("Location: ".$server."/login.php");
		// exit;
	} else {

		$sql = "SELECT * FROM mobile_tasker.users
				WHERE username LIKE '".strip_tags($_SERVER['PHP_AUTH_USER'])."' 
				AND password = '".md5(strip_tags($_SERVER['PHP_AUTH_PW']))."'
				AND activated = 1 
				";
		debugLog("login QRY: ".$sql);
		$res = mysqli_query($dbConn, $sql) or (debugLog("User database query failed. ERR: ".mysqli_error($dbConn)));
		$rws = mysqli_num_rows($res);

		if ($rws == 0) {
			header('HTTP/1.0 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="Management Access"');
			debugLog("No rows found!");
			$displayLogin = 1;
			// header("Location: ".$server."/login.php");
			// exit;
		} else {
			$rs = mysqli_fetch_assoc($res);
			$_SESSION['userId'] = $respArry['userId'] = $rs['user_id'];
			debugLog("LoggedIn: true, userId:".$_SESSION['userId']);
			// $provideResponse = true;
			$displayLogin = 0;
		}
	}
} else {
	debugLog("User session valid with ID: ".$_SESSION['userId']);
}



if ($provideResponse) {
	$response = array('status' 	=> 'success', 
					  'message' => $retvar,
					  'data' 	=> $respArry
					 );

	$resp = json_encode($response);
	debugLog('Auth Resp: '.$resp);

	header('Content-type: application/json');
	echo $resp;
}


function sanitize($post) {
    foreach ($post as $key => $val) {
        $post[$key] = strip_tags($val);
    }
    debugLog("Sanitized POST: ".print_r($post,1));
    return $post;
}

function getHost() {
	$proto = (empty($_SERVER['HTTPS'])) ? "http://" : "https://";
	return $proto.$_SERVER['SERVER_NAME'];
}

function debugLog($message) {
	error_log(date('Y-m-d H:i:s')." | ".$message."\n", 3, "/tmp/mobileTasker.log");
}