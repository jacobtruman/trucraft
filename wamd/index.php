<?
/*
 * TODO: setup app key
 * TOTO: menu
 * TODO: add "client" editor
 * TODO: update coord addition to use objects
 * TODO: add login
 */
//session_start();

require_once("AutoLoad.php");

$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";

?>
<!DOCTYPE html>
<html>
	<head>
		<link href="css/main.css" rel="stylesheet" type="text/css" />
		<link href="css/menu.css" rel="stylesheet" type="text/css" />
		<link href="css/jquery.loadmask.css" rel="stylesheet" type="text/css" />
		<link href="css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />

		<!--<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?=$api_key?>&sensor=false"></script>-->
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.ui.map.min.js"></script>
		<script type="text/javascript" src="js/jquery.loadmask.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.19.custom.min.js"></script>
		<script type="text/javascript" src="jsForms/editUser.js"></script>
		
		<script type="text/javascript">
			var script = '<script type="text/javascript" src="http://google-maps-' +
				'utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble';
			if (document.location.search.indexOf('compiled') !== -1) {
				script += '-compiled';
			}
			script += '.js"><' + '/script>';
			document.write(script);
		</script>
		<script type="text/javascript">
			var client_id;
			
			var pinShadow = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
				new google.maps.Size(40, 37),
				new google.maps.Point(0, 0),
				new google.maps.Point(12, 35));
			var infoBubbles = [];
			var markers = [];
			var map;
			var marker_color;
			var clients_array = [];

			function initialize() {
				map = new google.maps.Map(document.getElementById('map_canvas'), {
					zoom: 13,
					center: new google.maps.LatLng(0, 0),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				loadMarkers(true);
			}

			function loadMarkers(setCenter) {
				var stars = [];
				var star = false;
				var zindex = 0;
				latLang = new google.maps.LatLng(10, 20);
				$.getJSON( 'ajax/getCoords.php<?=$_SERVER['REQUEST_URI']?>', function(data) {
					clearMarkers();
					$.each( data.markers, function(i, marker) {
						star = false;
						if(!(marker.client.id in stars)) {
							star = true;
							stars[marker.client.id] = true;
						}
						latLang = new google.maps.LatLng(marker.latitude, marker.longitude);
						/*if(i == data.markers.length - 1)
							marker_color = "00FF00";
						else
							marker_color = marker.marker_color*/
						zindex = data.markers.length - i;
						markers[i] = new google.maps.Marker({
							position: latLang,
							map: map,
							bounds: true,
							zIndex: zindex,
							icon: getPinImage(marker.marker_color, zindex, data.markers.length, star)//,
							//shadow: pinShadow
						});
						
						clients_array[marker.client.id] = marker.client;

						if(setCenter) {
							map.setCenter(markers[0].getPosition());
							//map.setCenter(markers[i].getPosition());
						}
						latLang = new google.maps.LatLng(marker.latitude, marker.longitude);
						infoBubbles[i] = new InfoBubble({
							map: map,
							content: '<div class="phoneytext">'+marker.content+'<br />'+marker.client.name+'<br />'+marker.provider+'<br /><button onclick="editUser('+marker.client.id+')">edit user</button></div>',
							position: latLang,
							shadowStyle: 1,
							padding: 0,
							backgroundColor: 'rgb(57,57,57)',
							borderRadius: 4,
							arrowSize: 10,
							borderWidth: 1,
							borderColor: '#2c2c2c',
							//disableAutoPan: true,
							//hideCloseButton: true,
							arrowPosition: 30,
							backgroundClassName: 'phoney',
							arrowStyle: 2
						});
						google.maps.event.addListener(markers[i], 'click', function() {
							//map.setZoom(map.getZoom()+1);
							map.setCenter(markers[i].getPosition());
							if (!infoBubbles[i].isOpen()) {
								infoBubbles[i].open(map, markers[i]);
							}
						});
					});
				});
				setTimeout(function() { loadMarkers(false); },10000);
			}

			function clearMarkers() {
				for(var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
			}

			function getPinImage(color, num, total, star) {
				var pin_type = "d_map_xpin_letter_withshadow";
				var pin_style = "pin";
				var star_color = "00FF00";
				var font_color = "000000";
				if(star) {
					pin_style = "pin_star";
				}
				//pinImage = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + color,
				pinImage = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst='+pin_type+'&chld='+pin_style+'|'+num+'|'+color+'|'+font_color+'|'+star_color,
					new google.maps.Size(21, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(10, 34));
				return pinImage;
			}
		</script>
	</head>
	<body onload="initialize()">
		<div id="map_canvas"></div>
		<?
			require_once("navigation.php");
		?>
		<div id="editUserForm" title="Edit User"></div>
	</body>

	<script>
	function editUser(x)
	{
		client = clients_array[x];
		client_id = client.client_id;
		$("#editUserForm").dialog("open");
		//$("#name").val(client.name);
		//$("#client_id").val(client.client_id);
	}
	</script>
</html>
