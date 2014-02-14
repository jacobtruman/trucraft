<?php

require_once("AutoLoad.php");
//require_once("classes/DBConn.class.php");

$coords = new Coords();

//file_put_contents(dirname(__FILE__)."/errors.txt", implode("\n", array_keys($_REQUEST)), FILE_APPEND);
//file_put_contents(dirname(__FILE__)."/errors.txt", implode("\n", $_REQUEST), FILE_APPEND);
foreach($_REQUEST as $key=>$val) {
	$msgs[] = $key."=".$val;
}
file_put_contents(dirname(__FILE__)."/errors.txt", implode("&", $msgs), FILE_APPEND);

echo $coords->addCoordsOLD($_REQUEST);

?>
