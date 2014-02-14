<html>
	<head>
		<link href="css-dock-menu/style.css" rel="stylesheet" type="text/css" />
		<link href="../css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="css-dock-menu/js/interface.js"></script>
	</head>

	<body>
		<div class="dock" id="dock">
			<div class="dock-container">
				<a class="dock-item" href="index.php"><img src="css-dock-menu/images/map.png" alt="map" /><span>Map</span></a>
				<a class="dock-item" href="#"><img src="css-dock-menu/images/search.png" alt="search" id="search" /><span>Search</span></a>
				<a class="dock-item" href="#"><img src="css-dock-menu/images/settings.png" alt="settings" /><span>Settings</span></a>
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
	</body>
</head>
<?
	include("forms.php");
?>