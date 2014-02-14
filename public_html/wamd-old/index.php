<?

/*
This is the WaMd Main app
*/

/*
	@TODO: add multiple clients to same map
	@TODO: colorize clients differently
	@TODO: add information popup
	@TODO: add point differentiator - side bar perhaps
*/
// google maps api key
$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";
?>

<!DOCTYPE html>
<html>
	<head>
		<link href="css-dock-menu/style.css" rel="stylesheet" type="text/css" />
		<link href="../css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />
		<link href="../css/jquery.loadmask.css" rel="stylesheet" type="text/css" />

		<script src="../js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="../js/jquery.loadmask.js"></script>
		<script src="../js/jquery-ui-1.8.19.custom.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<? echo $api_key;?>&sensor=false"></script>
		<script type="text/javascript" src="css-dock-menu/js/interface.js"></script>
		<script type="text/javascript">
			function initialize()
			{
				$('#map').load('map.php');
			}
		</script>
	</head>

	<body onload="initialize()">
		<div class="dock" id="dock">
			<div class="dock-container">
				<a class="dock-item" href="index.php"><img src="css-dock-menu/images/map.png" alt="map" id="map" /><span>Map</span></a>
				<a class="dock-item" href="#"><img src="css-dock-menu/images/search.png" alt="search" id="search" /><span>Search</span></a>
				<a class="dock-item" href="#"><img src="css-dock-menu/images/settings.png" alt="settings" id="settings" /><span>Settings</span></a>
			</div> 
		</div>

		<!--dock menu JS options -->
		<script type="text/javascript">
			
			$(document).ready(
				function()
				{
					console.log("Fisheye Dock");
					$('#dock').Fisheye(
						{
							maxWidth: 50,
							items: 'a',
							itemsText: 'span',
							container: '.dock-container',
							itemWidth: 40,
							proximity: 90,
							valign: 'bottom',
							halign : 'center'
						}
					)
				}
			);

		</script>
		<div style="margin-left:10px;margin-right:10px;height:92%;" id="map"></div>
		<?
			include("forms.php");
		?>
	</body>
</html>