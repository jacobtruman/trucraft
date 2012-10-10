<?
/*
 * TODO: setup app key
 * TOTO: menu
 * TODO: add "client" editor
 * TODO: update coord addition to use objects
 * TODO: add login
 */
session_start();

require_once("AutoLoad.php");

$api_key = "ecb20ce40bdd4b09ac17ddc26f309fe3";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>TruCraft .::. CloudMade</title>
		
		<style>
			html
			{
				height: 100%;
			}

			body
			{
				height: 100%;
				margin: 0;
				padding: 0;
				
				font-size: 62.5%;
			}

			#map_canvas
			{
				width: 100%;
				height: 100%;
			}
			
			#menu div
			{
				margin-bottom: 2px;
				vertical-align: middle;
			}

			#menu div, #menu-tab
			{
				cursor: pointer;
			}

			#menu, #menu-tab
			{
				position:fixed;
				top:0;
				padding:5px;
			}
			
			#menu
			{
				font-size: 12px;
				font-family: "arial";
				padding-bottom:100%;
				padding-right:100px;
				padding-left:10px;
				background-color: #C0C0C0;
				color: #FFFFFF;
				border-right: 1px solid #000000;
				background: url(images/bg.png);
			}
			
			.menu-text
			{
				width: 50%;
				float: right;
			}
			
			.menu-image
			{
				float: left;
			}
			
			.clear
			{
				height: 0px;
				clear: both;
			}
			
			#settings .menu-image:before
			{
				content: url(images/cog_edit.png);
			}
			
			#home .menu-image:before
			{
				content: url(images/house.png);
			}

			#help .menu-image:before
			{
				content: url(images/help.png);
			}
			
			<!-- form box styles start -->
			label, input
			{
				display:block;
			}

			input.text
			{
				margin-bottom:12px;
				width:95%;
				padding: .4em;
			}
			
			fieldset
			{
				padding:0;
				border:0;
				margin-top:25px;
			}
			h1
			{
				font-size: 1.2em; margin: .6em 0;
			}
			
			div#users-contain
			{
				width: 350px;
				margin: 20px 0;
			}
			
			div#users-contain table
			{
				margin: 1em 0;
				border-collapse: collapse;
				width: 100%;
			}
			
			div#users-contain table td, div#users-contain table th
			{
				border: 1px solid #eee;
				padding: .6em 10px;
				text-align: left;
			}
			
			.ui-dialog .ui-state-error
			{
				padding: .3em;
			}
			
			.validateTips
			{
				border: 1px solid transparent;
				padding: 0.3em;
			}
			<!-- form box styles end -->
		</style>
		<link rel="stylesheet" href="js/CloudMade-Leaflet/dist/leaflet.css" />
		<link href="css/jquery.loadmask.css" rel="stylesheet" type="text/css" />
		<link href="css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="http://tile.cloudmade.com/wml/latest/web-maps-lite.js"></script>
		<script src="js/CloudMade-Leaflet/dist/leaflet.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.loadmask.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.19.custom.min.js"></script>

		<script type="text/javascript">
			var first_load = true;
			var map;
			var lat_low;
			var lat_high;
			var lon_low;
			var lon_high;
			var clients_array = [];
			var clients = new L.LayerGroup();
			var cloudmadeAttribution = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery � <a href="http://cloudmade.com">CloudMade</a>';
			var cloudmadeOptions = {maxZoom: 18, attribution: cloudmadeAttribution};
			var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/<?=$api_key?>/{styleId}/256/{z}/{x}/{y}.png';
			
			var cloudmade = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 997});
			var midnightCommander = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 999});
			var other = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 37008});
			var camo = new L.TileLayer(cloudmadeUrl, cloudmadeOptions, {styleId: 2607});

			function initialize()
			{
				console.log("Initialize map");
				map = new L.Map('map_canvas',
					{
						center: new L.LatLng(0, 0),
						zoom:10,
						layers: [cloudmade, clients]
					}
				);
				
				map.on('zoomend', function(e) {
					console.log(map.getZoom());
					map.setZoom(map.getZoom());
				});

				loadMarkers();
			}
			
			function loadMarkers()
			{
				$.getJSON( 'ajax/getCoords.php<?=$_SERVER['REQUEST_URI']?>', function(data)
				{
					$.each( data.markers, function(i, marker_obj)
					{
						var location = new L.LatLng(marker_obj.latitude, marker_obj.longitude);
						if(lat_low == null || marker_obj.latitude < lat_low)
							lat_low = marker_obj.latitude;
						if(lat_high == null || marker_obj.latitude > lat_high)
							lat_high = marker_obj.latitude;
						if(lon_low == null || marker_obj.longitude < lon_low)
							lon_low = marker_obj.longitude;
						if(lon_high == null || marker_obj.longitude > lon_high)
							lon_high = marker_obj.longitude;
						var myIcon = L.Icon.extend({
							iconUrl: "images/marker-red.png",
							shadowUrl: "images/marker-shadow.png"
						});
						
						clients_array[marker_obj.client.id] = marker_obj.client;
						
						var icon = new myIcon();
						var marker = new L.Marker(location, {icon:icon});
						//var marker = new L.Marker(location);
						var content = '<div class="phoneytext">'+marker_obj.content+"<br />"+marker_obj.client.name+'<br /><button onclick="editUser('+marker_obj.client.id+')">edit user</button></div>';
						marker.bindPopup(content);
						clients.addLayer(marker);
						console.log(map.getZoom());
						map.setView(location, map.getZoom());
					});
				}).complete(function() {
					var bounds = new L.LatLngBounds(new L.LatLng(lat_low, lon_low), new L.LatLng(lat_high, lon_high));
					console.log(map.getZoom());
					map.fitBounds(bounds);
					console.log(map.getZoom());
				});
				
				if(first_load)
				{
					var baseMaps = {
						"Normal": cloudmade,
						"Night View": midnightCommander,
						"Camo":camo,
						"Other": other
					};
					var overlayMaps = {
						"Clients": clients
					};
					layersControl = new L.Control.Layers(baseMaps, overlayMaps);
					map.addControl(layersControl);
					first_load = false;
				}
				
				// refresh every 10 seconds
				setTimeout(function() { loadMarkers(); },10000);
			}
			
			$(function()
			{
				// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
				$( "#dialog:ui-dialog" ).dialog( "destroy" );

				var name = $( "#name" ),
					//email = $( "#email" ),
					//password = $( "#password" ),
					//allFields = $( [] ).add( name ).add( email ).add( password ),
					allFields = $( [] ).add( name ),
					tips = $( ".validateTips" );

				function updateTips( t ) {
					tips
						.text( t )
						.addClass( "ui-state-highlight" );
					setTimeout(function() {
						tips.removeClass( "ui-state-highlight", 1500 );
					}, 500 );
				}

				function checkLength( o, n, min, max ) {
					if ( o.val().length > max || o.val().length < min ) {
						o.addClass( "ui-state-error" );
						updateTips( "Length of " + n + " must be between " +
							min + " and " + max + "." );
						return false;
					} else {
						return true;
					}
				}

				function checkRegexp( o, regexp, n ) {
					if ( !( regexp.test( o.val() ) ) ) {
						o.addClass( "ui-state-error" );
						updateTips( n );
						return false;
					} else {
						return true;
					}
				}

				$( "#editUserForm" ).dialog({
					autoOpen: false,
					height: 300,
					width: 350,
					modal: true,
					buttons: {
						"Submit": function() {
							var bValid = true;
							allFields.removeClass( "ui-state-error" );

							bValid = bValid && checkLength( name, "username", 3, 16 );
							//bValid = bValid && checkLength( email, "email", 6, 80 );
							//bValid = bValid && checkLength( password, "password", 5, 16 );

							bValid = bValid && checkRegexp( name, /^[a-z]([0-9 a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, spaces, begin with a letter." );
							// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
							//bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
							//bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

							if ( bValid ) {
								$.ajax({
									type: "POST",
									url: "ajax/updateClient.php",
									data: { name: $("#name").val(), client_id: $("#client_id").val() }
								}).done(function( msg ){  
									$("#editUserForm").dialog("close");
								});
							}
						},
						Cancel: function() {
							$( this ).dialog( "close" );
						}
					},
					close: function() {
						allFields.val( "" ).removeClass( "ui-state-error" );
					}
				});

				$( "#create-user" )
					.button()
					.click(function() {
						$( "#editUserForm" ).dialog( "open" );
					});
			});
		</script>
	</head>
	<body>
		<div id="map_canvas"></div>
		<?//MENU START?>
		<div id="menu">
			<div id="menu-bg">
			<div id="home">
				<div class="menu-image">&nbsp;</div>
				<div class="menu-text">Home</div>
				<div class="clear"></div>
			</div>
			<div id="settings">
				<div class="menu-image">&nbsp;</div>
				<div class="menu-text">Settings</div>
				<div class="clear"></div>
			</div>
			<div id="help">
				<div class="menu-image">&nbsp;</div>
				<div class="menu-text">Help</div>
				<div class="clear"></div>
			</div>
			<div id='hide'><img src="images/cog_delete.png" /></div>
			</div>
		</div>
		<div id="menu-tab">
			<img src="images/cog_add.png" />
		</div>
		
		<div id="editUserForm" title="edit user">
			<form>
				<fieldset>
					<label for="name">Name</label>
					<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
					<input type="hidden" name="client_id" id="client_id" />
				</fieldset>
			</form>
		</div>
		<?//MENU END?>

		<script>
			$(document).ready(function() {
				initialize();
			});
			
			/*********
			MENU START
			*********/
			<?
				if($_SESSION['menu_open'])
					echo '$("#menu-tab").hide();';
				else
					echo '$("#menu").hide();';
			?>
			$("#menu-tab").click(function() {
				$("#menu-tab").hide("slow");
				$("#menu").show("slow");
				$.ajax({url: "toggle_session_var.php?menu_open=1"});
			});
			
			$("#hide").click(function() {
				$("#menu-tab").show("slow");
				$("#menu").hide("slow");
				$.ajax({url: "toggle_session_var.php?menu_open=0"});
			});
			
			$("#home").click(function() {
				document.location = "<?=$_SERVER['PHP_SELF']?>";
			});
			
			$("#help").click(function() {
				alert("coming soon");
			});
			
			$("#settings").click(function() {
				alert("coming soon");
			});
			
			function editUser(x)
			{
				client = clients_array[x];
				$("#editUserForm").dialog("open");
				$("#name").val(client.name);
				$("#client_id").val(client.client_id);
			}
		</script>
	</body>
</html>	