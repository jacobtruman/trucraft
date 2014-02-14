<?
$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";
?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			html { height: 100% }
			body { height: 100%; margin: 0; padding: 0 }
			#map_canvas { width:100%; height: 100% }
		</style>

		<!--<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?=$api_key?>&sensor=false"></script>-->
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.ui.map.min.js"></script>
		<script type="text/javascript">
			function initialize()
			{
				$('#map_canvas').gmap({'disableDefaultUI':true, 'callback': function()
				{
					var self = this;
					$.getJSON( 'http://trucraft.net/ajax/getCoords.php', function(data)
					{
						$.each( data.markers, function(i, marker)
						{
							console.log(i);
							console.log(marker);
							self.addMarker({ 'position': new google.maps.LatLng(marker.latitude, marker.longitude), 'bounds':true } ).click(function()
							{
								self.openInfoWindow({ 'content': marker.content }, this);
							});
						});
					});
				}});
			}
		</script>
	</head>
	<body onload="initialize()">
		<div id="map_canvas"></div>
	</body>
</html>
