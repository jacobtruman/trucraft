<?php

/*
 * @TODO: add disable response
 * @TODO: add location check interval change response
 */

require_once("classes/DBConn.class.php");

$db_fields = array("client_id", "lat", "lon", "provider", "location_time", "location_timezone", "battery", "accuracy", "speed", "altitude", "bearing");

$db = new DBConn();

$params = array();

foreach($db_fields as $field)
{
	if(isset($_REQUEST[$field]))
	{
		$params[$field] = $_REQUEST[$field];
	}
}

/*if(isset($params['location_time']))
{
	$params['location_time'] = date("Y-m-d H:i:s", $params['location_time']);
}*/

if(!isset($params['lat']) || !isset($params['lat']))
{
	$coords = explode("_", $_REQUEST['coords']);
	$params['lat'] = (isset($_REQUEST['lat']) ? $_REQUEST['lat'] : $coords[0]);
	$params['lon'] = (isset($_REQUEST['lon']) ? $_REQUEST['lon'] : $coords[1]);
}

$sql = "INSERT INTO coords SET ";
foreach($params as $key=>$val)
{
	$sql .= $key." = '".$val."',";
}
$sql .= "date = NOW()";

$db->query($sql);

echo "SUCCESS";

?>
