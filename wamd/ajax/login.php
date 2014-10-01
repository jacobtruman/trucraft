<?php
//ini_set('display_errors', 1);
require_once("../AutoLoad.php");

$auth_hash = json_decode(Auth::login($_REQUEST), true);

if(isset($auth_hash['auth_id']))
{
	$_SESSION['auth_id'] = $auth_hash['auth_id'];
	echo "SUCCESS";
} else {
	echo "FAILURE";
}

?>
