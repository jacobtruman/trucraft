<?php
session_start();

function __autoload($class_name) {

	$found = false;
	$dirs = array(
		dirname(__FILE__).'/classes/'
	);
	$exts = array(
		'.class.php',
		'.interface.php'
	);

	foreach($dirs as $dir)
	{
		foreach($exts as $ext)
		{
			if(file_exists($dir . $class_name . $ext))
			{
    			require_once($dir . $class_name . $ext);
    			$found = true;
    			break;
			}
		}
	}
}

if($_SERVER['PHP_SELF'] !== "/add_coords.php") {
	$auth = new Auth($_SESSION['auth_id']);

	$login_page = "/login.php";
	if(!$auth->isAuthenticated() && !strstr($_SERVER["SCRIPT_NAME"], $login_page))
	{
		header("Location: "."http://".$_SERVER["HTTP_HOST"].$login_page);
	}

	$_SESSION['group'] = $auth->group;
}
?>
