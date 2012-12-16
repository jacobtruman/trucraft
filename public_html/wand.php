<?php

require_once("classes/DBConn.class.php");

$db = new DBConn();

$coords = explode("_", $_REQUEST['coords']);
$lat = $coords[0];
$lon = $coords[1];

$sql = "INSERT INTO coords SET client_id = ".$_REQUEST['client_id'].", lat = '".$lat."', lon = '".$lon."', date = NOW()";

$db->query($sql);

?>
