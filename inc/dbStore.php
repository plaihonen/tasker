<?php
/*

Store the actions submitted from the tasker app into the local database.

By 
Petri Laihonen (pietu@charlotteandpetri.net)
20.10.2013

*/

// Database credentials
require_once("./dbCredentials.php");

// response array
$response = array();
$resp = "";

if (!$dbConn = mysqli_connect($conn, $dbUser, $dbPass, $database)) {
	$response = array('status' 	=> 'fail', 
					  'message' => 'database connect error.'
					 );
	debugLog('Database Connection Failed. Error:'.mysqli_error());
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
	    [siteType] => rivitalo
	    [userId] => 1
	)
	*/

	// do a rudimentary sanitation for the post values
	$_POST = sanitize($_POST);
	debugLog('Sanitized POST: '.print_r($_POST,1));

	// Default User ID
	if (empty($_POST['userId'])) $_POST['userId'] = 1;

	// temp measure
	// if ($_POST['userId'] != 1) {
	// 	debugLog("Changing userId from ".$_POST['userId']." to 1");
	// 	$_POST['userId'] = 1;
	// }

	// add new user
	if ($_POST['action'] == "register") {
		$sql = "INSERT INTO users (username, password, email, reg_date, group_id) 
				VALUES ('".$_POST['username']."','".md5($_POST['password'])."','".$_POST['username']."',NOW(),'".$_POST['group_id']."') ";
		debugLog('Inserting user with SQL: '.$sql);

		if (strlen($_POST['password']) >= 6) {
			try {

				// execute statement
				if ($res = mysqli_query($dbConn, $sql)) {
					$insertId = mysqli_insert_id($dbConn);
							
					$response = array('status' 	=> 'success', 
									  'message' => 'database connect ['.$_POST['action'].'] success.',
									  'data' => array('userId' => $insertId)
									 );
					debugLog($_POST['action']." Query completed with [".$insertId."] ID created.");

				} else {
					debugLog('Query Not executed');
					debugLog('Query SQL: '.$sql);
				}

			} catch (Exception $e) {

				$response = array('status' 	=> 'fail', 'message' => 'database INSERT error.');
				debugLog('ERROR: executing query: '.print_r($e->getMessage(),1));
				debugLog('ERROR: SQL Error: '.mysqli_error($dbConn).' SQL: '.$sql);
				debugLog('SQL: '.$sql);

			}
		} else {
			$response = array('status' 	=> 'fail', 'message' => 'Password too short Must be at least 6 characters.');
		}
	}


	if ($_POST['action'] == "insert" || $_POST['action'] == "insertMulti" || $_POST['action'] == "update") {

		// Insert new Site
		if ($_POST['action'] == "insert" || $_POST['action'] == "insertMulti") {

			if ($_POST['formName'] == "editor" || $_POST['formName'] == "creator") {
				debugLog('Storing from Form Editor');
				$sql = "INSERT INTO sites (userId, siteName, siteType, siteAddress, siteZip, siteCity, inserted)
						VALUES (".$_POST['userId'].", '".$_POST['siteName']."', '".$_POST['siteType']."', '".$_POST['siteAddress']."', 
								".$_POST['siteZip'].", '".$_POST['siteCity']."', NOW())";
			}

			if ($_POST['formName'] == "tasker") {
				debugLog('Storing from Form Tasker');
				$sql = "INSERT INTO tasks (userId, siteId, taskType, notes, entryDate)
						VALUES (".$_POST['userId'].", '".$_POST['siteId']."', '".$_POST['taskType']."', 
								'".$_POST['taskNote']."', NOW())";
			}

			if ($_POST['formName'] == "taskerMulti") {
				// Insert multiple sites at once
				debugLog('Storing from Form TaskerMulti');

				$insertArry = array();
				$idArry = $_POST['include'];

				debugLog('idArry: '.print_r($idArry,1));

				foreach($idArry as $siteIdX) {
					$siteId = mysqli_real_escape_string($dbConn, $siteIdX);
					$insString = "(".$_POST['userId'].",'".$siteId."','".$_POST['taskSelectType']."', 
									'".$_POST['taskSelectNote']."', NOW())"; 
					array_push($insertArry, $insString);
				}

				$sql = "INSERT INTO tasks (userId, siteId, taskType, notes, entryDate) VALUES ".implode(",",$insertArry);
				debugLog('Multi ID Insert: '.$sql);
			}

		// Update existing site data
		} else if ($_POST['action'] == "update" && !empty($_POST['id'])) {

			if ($_POST['delete'] == "yes") $hide = " visible = 0, ";

			$sql = "UPDATE sites SET siteName = '".$_POST['siteName']."',
									 siteAddress = '".$_POST['siteAddress']."',
									 siteZip = ".$_POST['siteZip'].", 
									 siteCity = '".$_POST['siteCity']."',
									 siteType = '".$_POST['siteType']."',
									 $hide
									 modified = NOW() 
					WHERE id = '".$_POST['id']."' ";

		} else {
			// default select, pretty much always
			$_POST['action'] = "select";

		}

		try {

			// execute statement
			if ($res = mysqli_query($dbConn, $sql)) {
				$count = mysqli_affected_rows($dbConn);

				$response = array('status' 	=> 'success', 
								  'message' => 'database connect ['.$_POST['action'].'] success.'
								 );
				debugLog($_POST['action']." Query completed with [".$count."] affected rows.");

			} else {
				debugLog('Query Not executed');
				debugLog('Query SQL: '.$sql);
			}

		} catch (Exception $e) {

			$response = array('status' 	=> 'fail', 
							  'message' => 'database INSERT error.'
							 );
			debugLog('ERROR: executing query: '.print_r($e->getMessage(),1));
			debugLog('ERROR: SQL Error: '.mysqli_error($dbConn).' SQL: '.$sql);
			debugLog('SQL: '.$sql);

		}

	}

	// Read stats for the current user
	if ($_POST['action'] == "selectStats") {

		debugLog("SELECT Stats Query.....");

		$dateStart = !empty($_POST['dateStart']) ? date("Y-m-d", strtotime($_POST['dateStart'])) : "2013-11-01";
		$dateEnd = !empty($_POST['dateEnd']) ? date("Y-m-d", strtotime($_POST['dateEnd'])) : "2021-12-31";

		$timeFrame = " AND DATE_FORMAT(t.entryDate, '%Y-%m-%d') BETWEEN '".$dateStart."' AND '".$dateEnd."' ";

	    $sql = "SELECT t.taskType, t.notes, DATE_FORMAT(t.entryDate, '%d-%m-%Y') as entryDate, 
	    			   s.siteName, s.siteType, s.siteAddress, s.siteZip, s.siteCity 
	    		FROM tasks t
	    		JOIN sites s ON s.id = t.siteId 
	    		WHERE t.userId = ".$_POST['userId']."
	    		$timeFrame 
	    		";

	    // debug
		debugLog('Stats SQL: '.$sql);

		if ($res = mysqli_query($dbConn, $sql)) {

			$rows = mysqli_num_rows($res);

			debugLog("Select returned ".$rows." rows.");
		    
		    $dataArry = array();

			while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
				// debugLog("Row: ".print_r($row,1));
				array_push($dataArry, $row);
			}

			// ajax data filled with arry
			$response = array('status' 	=> 'success', 
							  'message' => 'database connect ['.$_POST['action'].'] success.',
							  'data' 	=> $dataArry
							 );

		} else {
			debugLog('Statistics Query Not executed!!');
		}


	// Read sites for the current user
	} else if ($_POST['action'] == "select") {
	    // $sql = "SELECT * FROM sites WHERE userId = ".$_POST['userId'];
		debugLog("SELECT Query.....");
	    $sql = "SELECT * FROM sites WHERE userId = ".$_POST['userId']." AND visible = 1 ";

		if ($res = mysqli_query($dbConn, $sql)) {

			$rows = mysqli_num_rows($res);

			debugLog("Select returned ".$rows." rows.");
		    
		    $dataArry = array();

			while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
				// debugLog("Row: ".print_r($row,1));
				array_push($dataArry, $row);
			}

			// ajax data filled with arry
			$response = array('status' 	=> 'success', 
							  'message' => 'database connect ['.$_POST['action'].'] success.',
							  'data' 	=> $dataArry
							 );

		} else {
			debugLog('Query Not executed AGAIN!!');
		}
	} 

	mysqli_free_result($res);

	$resp = json_encode($response);
	// debugLog('dbStore Resp: '.$resp);

	header('Content-type: application/json');
	echo $resp;

} else {
	debugLog('No POST values');
}


function debugLog($message) {
	# error_log(date('Y-m-d H:i:s')." | ".$message."\n", 3, "mobileTasker.log");
	# echo("<!-- ".date('Y-m-d H:i:s')." | ".$message."<br>\n -->");
}

function sanitize($post) {
	global $dbConn;
	$escaped = array();
	foreach ($post as $key => $value) {
		// Skip "include" field, since it will be an array
		if ($key == 'include') {
			$escaped[$key] = $value;
		} else {
			$escaped[$key] = mysqli_real_escape_string($dbConn, $value);
		}
	}
	return $escaped;
}
