<?
require_once("../classes/DBConn.class.php");

// google maps api key
$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";
// default map center
$center = "0, 0";
$zoom = 15;
$db = new DBConn();

$lookback = isset($_REQUEST['lookback']) ? $_REQUEST['lookback'] : 1;

$sql = "SELECT * FROM coords WHERE ";

if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date']))
{
	if($_REQUEST['start_date'] > $_REQUEST['end_date'])
	{
		$temp = $_REQUEST['start_date'];
		$_REQUEST['start_date'] = $_REQUEST['end_date'];
		$_REQUEST['end_date'] = $temp;
		unset($temp);
	}
	$params['location_time'] = "BETWEEN '".date("Y-m-d", strtotime($_REQUEST['start_date']))."' AND '".date("Y-m-d", strtotime($_REQUEST['end_date']))."'";
}
else if(isset($_REQUEST['start_date']))
	$params['location_time'] = ">= '".date("Y-m-d", strtotime($_REQUEST['start_date']))."'";
else
	$params["location_time"] = "> '".date("Y-m-d H:i:s", strtotime("-".$lookback." hour"))."'";

if(isset($_REQUEST['client_id']))
	$params["client_id"] = "= '".$_REQUEST['client_id']."'";

$param_str = "";
foreach($params as $param=>$value)
{
	if(!empty($param_str))
		$param_str .= " AND ";
	$param_str .= $param." ".$value;
}
$sql .= $param_str." ORDER BY location_time";
//var_dump($sql);

$res = $db->query($sql);

$clients = array();

while($row = $res->fetch_assoc())
{
	$coord = $row['lat'].", ".$row['lon'];
	$center = $coord;
	$clients[$row['client_id']][$row['location_time']] = $coord;
}

if(count($clients) <= 0)
{
	echo "No gps coordinates found with the specified parameters";
	exit;
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
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<? echo $api_key;?>&sensor=false"></script>
		<script type="text/javascript" src="../js/infobox.js"></script>
		<script>
			console.log("Map page loading");
			// http://code.google.com/apis/maps/articles/phpsqlajax_v3.html
			// Change this depending on the name of your PHP file
			/*
			function showGetResultkml()
			{
				var result = null;
				var scriptUrl = "http://www.kml";
				$.ajax(
				{
					url: scriptUrl,
					type: 'get',
					dataType: 'xml',
					async: false,
					success: function(data)
					{
						result = data;
						var xml = data.responseXML;
						var markers = xml.documentElement.getElementsByTagName("marker");

						for (var i = 0; i < markers.length; i++)
						{
							var name = markers[i].getAttribute("name");
							var address = markers[i].getAttribute("address");
							var type = markers[i].getAttribute("type");
							var point = new google.maps.LatLng( parseFloat(markers[i].getAttribute("lat")), parseFloat(markers[i].getAttribute("lng")));

							var html = "<b>" + name + "</b> <br/>" + address;
							var icon = customIcons[type] || {};

							var marker = new google.maps.Marker(
							{
								map: map,
								position: point,
								icon: icon.icon,
								shadow: icon.shadow
							});

							bindInfoWindow(marker, map, infoWindow, html);
						}
					}                          
				});

				function bindInfoWindow(marker, map, infoWindow, html)
				{
					google.maps.event.addListener(marker, 'click', function()
					{
						infoWindow.setContent(html);
						infoWindow.open(map, marker);
					});
				}

				return result;
			}
			*/

			var pinColors = ["68d752", "af0025", "5e6dff"];
			var pinImages = [];
			for(var i = 0; i < pinColors.length; i++)
			{
				//console.log("Building color pins: "+i);
				pinImages[i] = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + pinColors[i],
					new google.maps.Size(21, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(10, 34));
			}
			var pinShadow = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
				new google.maps.Size(40, 37),
				new google.maps.Point(0, 0),
				new google.maps.Point(12, 35));
			
			function initMarkers(theMap, markerData)
			{
				console.log("Build markers");
				var newMarkers = [];
				var marker;
				var latlngbounds = new google.maps.LatLngBounds();
				var i = 0;

				for(i = 0; i < markerData.length; i++)
				{
					//console.log("Marker "+i);
					marker = new google.maps.Marker({
						map: theMap,
						draggable: true,
						position: markerData[i].latLng,
						visible: true,
						icon: pinImages[markerData[i].colorKey],
						shadow: pinShadow,
						zIndex: i
					});
					
					// extend the bounds of the visible map
					latlngbounds.extend(markerData[i].latLng);

					// add marker to array of markers
					newMarkers.push(marker);

					//define the text and style for all infoboxes
					var boxText = document.createElement("div");
					boxText.style.cssText = "border: 1px solid black; margin-top: 8px; background:#333; color:#FFF; font-family:Arial; font-size:12px; padding: 5px; border-radius:6px; -webkit-border-radius:6px; -moz-border-radius:6px;";
					boxText.innerHTML = markerData[i].client_id+"<br />"+markerData[i].date;

					//define the options for all infoboxes
					var myOptions =
					{
						content: boxText,
						disableAutoPan: false,
						maxWidth: 0,
						pixelOffset: new google.maps.Size(-140, 0),
						zIndex: i+1000,
						boxStyle:
						{
							background: "url('images/tipbox.gif') no-repeat",
							opacity: 0.75,
							width: "280px"
						},
						closeBoxMargin: "12px 4px 2px 2px",
						closeBoxURL: "images/close.gif",
						infoBoxClearance: new google.maps.Size(1, 1),
						isHidden: false,
						pane: "floatPane",
						enableEventPropagation: false
					};

					//Define the infobox
					//console.log("Build info box: "+i);
					newMarkers[i].infobox = new InfoBox(myOptions);
					//console.log(newMarkers[i].infobox);
					//Open box when page is loaded
					newMarkers[i].infobox.open(theMap, marker);
					newMarkers[i].infobox.close(theMap, marker);

					//Open infobox for marker when user clicks on it.  This code pattern, with the callback returning a function, is needed to
					//create closure.  This pattern is often needed when using callbacks inside a for-loop.  If you used a normal callback (in 
					//which there is no inner function), there would be no closure and all markers would open the infobox of the last marker
					//created in the for-loop
					google.maps.event.addListener(marker, 'click', (function(marker, i)
					{
						return function()
						{
							for ( h = 0; h < newMarkers.length; h++ )
							{
								//console.log("Close info box: "+h);
								newMarkers[h].infobox.close();
							}
							newMarkers[i].infobox.open(theMap, this);
							theMap.panTo(markerData[i].latLng);
						}
					})(marker, i));
					//console.log(i+" Markers");
				}
				theMap.fitBounds( latlngbounds );
				//theMap.setCenter( latlngbounds.getCenter( ), theMap.getBoundsZoomLevel( latlngbounds ) );
				console.log(i+" Markers Built");
				//return newMarkers;
			}

			function initialize()
			{
				console.log("Initializing");
				var myOptions =
				{
					center: new google.maps.LatLng(<?=$center?>),
					zoom: <?=$zoom?>,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				<?
					$coords_arr = array();
					$client_keys = array_flip(array_keys($clients));
					foreach($clients as $client_id=>$coords)
					{
						foreach($coords as $date=>$coord)
						{
							$coords_arr[] = "{latLng: new google.maps.LatLng(".$coord."), date: '".$date."', client_id: '".$client_id."', colorKey: '".$client_keys[$client_id]."'}";
						}
					}
				?>
				//var markers = 
					initMarkers(map, [<?=implode(",\n", $coords_arr)?>]);
			}
			console.log("Map page loaded");
			initialize();
		</script>
	</head>
	<div id="map_canvas" style="width:100%;height:100%"></div>
</html>