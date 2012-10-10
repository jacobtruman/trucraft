<?php
require_once("../classes/DBConn.class.php");

$db = new DBConn();

$lookback = isset($_REQUEST['lookback']) ? $_REQUEST['lookback'] : 1;

$sql = "SELECT * FROM coords WHERE date > '".date("Y-m-d H:i:s", strtotime("-".$lookback." hour"))."' ORDER BY location_time";

$res = $db->query($sql);

$coords = array();
while($row = $res->fetch_assoc())
{
	$coord['latitude'] = $row['lat'];
	$coord['longitude'] = $row['lon'];
	$coord['title'] = $row['date'];
	$coord['content'] = "test";
	$coords[] = $coord;
}

echo json_encode(array("markers"=>$coords));

//{"markers":[ { "latitude":57.7973333, "longitude":12.0502107, "title":"Angered", "content":"Representing :)" }, { "latitude":57.6969943, "longitude":11.9865, "title":"Gothenburg", "content":"Swedens second largest city" } ]}


?>