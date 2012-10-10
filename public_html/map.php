<?php
require_once("classes/DBConn.class.php");

// google maps api key
$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";
// default map center
$center = "0, 0";
$zoom = 15;

$db = new DBConn();


$lookback = isset($_REQUEST['lookback']) ? $_REQUEST['lookback'] : 1;
$client_id = isset($_REQUEST['client_id']) ? $_REQUEST['client_id'] : "8018574316";

$sql = "SELECT * FROM coords WHERE client_id = '".$client_id."' AND date > '".date("Y-m-d H:i:s", strtotime("-".$lookback." hour"))."' ORDER BY location_time";

$res = $db->query($sql);

while($row = $res->fetch_assoc())
{
	$coord = $row['lat'].", ".$row['lon'];
	$center = $coord;
	if($zoom == 0)
		$zoom = 20;
	$coords[$row['date']] = $coord;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?=$api_key?>&sensor=false"></script>
    <script type="text/javascript">
	//var pinColor = "FE7569";
	/*var pinColor = "FF0000";
    var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));*/

      function initialize() {
        var myOptions = {
          center: new google.maps.LatLng(<?=$center?>),
          zoom: <?=$zoom?>,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		<?
			$i = 0;
			foreach($coords as $date=>$coord)
			{
				echo "new google.maps.Marker({
					map: map,
					position: new google.maps.LatLng(".$coord."),
					title: '".date("m/d/Y H:i:s", strtotime($date))."',
					//icon: pinImage,
					//shadow: pinShadow,
					zIndex: ".$i++."
				});";
			}
		?>
      }
    </script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
