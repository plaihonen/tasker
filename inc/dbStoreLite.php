<?php
/*

Store the actions submitted from the tasker app into the local database.

By 
Petri Laihonen (pietu@charlotteandpetri.net)
21.10.2013

*/

// Database credentials
require_once("./dbCredentials.php");

// response array
$response = array();

try {
	/*** connect to SQLite database ***/
    $dbConn = new PDO("sqlite:db/tasker.sdb");

} catch(PDOException $e) {
	$response['status']  = "fail";
	$response['message'] = "database connect error";
	debugLog('Database Creation/Connection Failed. Error:'.$e->getMessage());
	echo json_encode($response);
	exit;
}

// received values
if (!empty($_POST)) {
	debugLog('POST: '.print_r($_POST,1));
	/*
	(
	    [siteName] => My new site
	    [siteAddress] => new street
	    [siteZip] => 123654
	    [siteCity] => new City
	    [userId] => 1
	)
	*/

	// do a rudimentary sanitation for the post values
	$_POST = sanitize($_POST);
	debugLog('Sanitized POST: '.print_r($_POST,1));

	// Default User ID
	if (empty($_POST['userId'])) $_POST['userId'] = 1;

	// Insert new Site
	if ($_POST['action'] == "insert") {

		$sql = "INSERT INTO sites (userId, siteName, siteAddress, siteZip, siteCity, inserted)
				VALUES (".$_POST['userId'].", '".$_POST['siteName']."', '".$_POST['siteAddress']."', 
				".$_POST['siteZip'].", '".$_POST['siteCity']."', '".date('Y-m-d H:i:s')."') ";

	// Update existing site data
	} else if ($_POST['action'] == "update" && !empty($_POST['id'])) {

		$sql = "UPDATE sites SET siteName = '".$_POST['siteName']."',
								 siteAddress = '".$_POST['siteAddress']."',
								 siteZip = ".$_POST['siteZip'].", 
								 siteCity = '".$_POST['siteCity']."',
								 modified = '".date('Y-m-d H:i:s')."' 
				WHERE id = '".$_POST['id']."' ";

	} else {
		// default select, pretty much always
		$_POST['action'] = "select";

	}


	try {

		$count = $dbConn->exec($sql);

		$response['status']  = "success";
		$response['message'] = "database connect ".$_POST['action']." success.";
		debugLog($_POST['action']." Query completed with [".$count."] affected rows.");

	} catch(PDOException $e) {

		$response['status']  = "fail";
		$response['message'] = "database INSERT error";
		debugLog('Database '.$_POST['action'].' Error:'.$e->getMessage());
		debugLog('SQL: '.$sql);

	}

	// Read sites for the current user
	if ($response['status'] == "success" || $_POST['action'] == "select") {

	    $sql = "SELECT * FROM sites WHERE userId = ".$_POST['userId'];
	    $dataArry = array();

	    foreach ($dbConn->query($sql) as $row) {
			// print $row['animal_type'] .' - '. $row['animal_name'] . '<br />';
			array_push($dataArry, $row);
		}
		// ajax data filled with arry
		$response['data'] = $dataArry;

	}

	// Close the DB connection
 	$dbConn = null;

	// response to the script
	echo json_encode($response);

} else {

	debugLog('No POST values');

}


function debugLog($message) {
	error_log(date('Y-m-d H:i:s')." | ".$message."\n", 3, "/tmp/mobileTasker.log");
}

function sanitize($post) {
	$escaped = array();
	foreach ($post as $key => $value) {
		$escaped[$key] = stripslashes($value);
	}
	return $escaped;
}