<?php
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
?>