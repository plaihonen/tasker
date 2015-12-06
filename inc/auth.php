<?php
/**
 *	Class for authenticating the site
 *	
 *	by Petri Laihonen 17.12.2013
 *	
 */


$provideResponse = true;
$salt = "æøæ|asdrSDRAG12556AWQ899REG1WqerG#¤%#%&¤#YWRGHåäG?Rq+oew£#»qwer";
$server = getHost();
debugLog("Server: ".$server);

// sanitize some
$_POST = sanitize($_POST);

// Database credentials
require_once("dbCredentials.php");
// require uFlex class
require_once("class.uFlex.php");

debugLog("Required includes are now with us");

//This creates the object
$user = new uFlex(false);

/*
* You may now configure the uFlex object before starting it
*/

$user->db['host'] = $conn;
$user->db['user'] = $dbUser;
$user->db['pass'] = $dbPass;
$user->db['name'] = $database; //Database name


debugLog("Page: ".$_SERVER['PHP_SELF']);

//Starts the object and Resume the users current session
$user->start();

debugLog("POST mode: ".$_POST['mode']);

$mode = $_POST['mode'];
unset($_POST['mode']);

switch($mode){
    
    case "login":

		$username = $_POST['username'];
		$password = $_POST['password'];
		$auto = $_POST['auto'];  //To remember user with a cookie for autologin

		$user = new uFlex();

		//Login with credentials
		$user->login($username,$password,$auto);

		//not required, just an example usage of the built-in error reporting system
		if($user->signed){
			$retvar = "User Successfully Logged in";
			debugLog("Return: ".$retvar);
			// echo $retvar;
		}else{
			//Display Errors
			foreach($user->error() as $err){
				$retvar = "Error: {$err}";
				debugLog("Return: ".$retvar);
				// echo $retvar;
			}
		}

	break;


    case "register":

		$registered = $user->register($_POST,false);

		if($registered){
			$retvar = "User Registered";
			debugLog("Return: ".$retvar);
			// echo $retvar;
		}else{
			//Display Errors
			foreach($user->error() as $err){
				$retvar = "Error: {$err}";
				debugLog("Return: ".$retvar);
				// echo $retvar;
			}
		}

		break;


    case "logout":

		//Logouts user and clears any auto login cookie
		$user->logout();

	break;

	default:
		// check if we are still not logged in
		// if (stripos($_SERVER['PHP_SELF'], "login.php") == 0) {
			if ($user->signed == false) {
				$forwardTo = $server."/login.php";
				debugLog('We are not signed in. Forwarding to : ' . $forwardTo);
				header("location: ".$forwardTo);
				exit;
			}
		// }
		$provideResponse = false;
}

debugLog("uFlex | REPORT : ".print_r($user->report(),1));
debugLog("uFlex | ERROR : ".print_r($user->error(),1));
debugLog("uFlex | FORM : ".print_r($user->form_error(),1));

if ($provideResponse) {
	$response = array('status' 	=> 'success', 
					  'message' => $retvar,
					  'data' 	=> ''
					 );

	$resp = json_encode($response);
	debugLog('dbStore Resp: '.$resp);

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
	# error_log(date('Y-m-d H:i:s')." | ".$message."\n", 3, "mobileTasker.log");
	# echo("<!-- ".date('Y-m-d H:i:s')." | ".$message."<br>\n -->");
}