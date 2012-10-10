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

?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			html { height: 100% }
			body { height: 100%; margin: 0; padding: 0 }
			
			#menu div
			{
				margin-bottom: 2px;
				vertical-align: middle;
				cursor: pointer;
			}

			#menu
			{
				position:fixed;
				top:0;
				width:200px;;
				padding:5px;
				font-size: 12px;
				font-family: "arial";
				padding-bottom:100%;
				padding-right:5px;
				padding-left:10px;
				background-color: #C0C0C0;
				color: #FFFFFF;
				border-right: 1px solid #000000;
				background: url(images/bg.png);
			}
			
			.menu-text
			{
				width: 80%;
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
			
			#google .menu-image:before
			{
				content: url(images/google-maps.png);
			}
			
			#cloudmade .menu-image:before
			{
				content: url(images/cloudmade-maps.png);
			}
		</style>
		<link href="css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>>
		<script type="text/javascript" src="js/jquery-ui-1.8.19.custom.min.js"></script>
	</head>
	<body>
		<div id="menu">
			<div id="menu-bg" style="width: 100%;">
				<div id="google">
					<div class="menu-image">&nbsp;</div>
					<div class="menu-text">Google Maps</div>
					<div class="clear"></div>
				</div>
				<div id="cloudmade">
					<div class="menu-image">&nbsp;</div>
					<div class="menu-text">CloudMade Maps</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</body>
	
	<script>
		$("#google").click(function() {
			document.location = "index-google.php";
		});
		
		$("#cloudmade").click(function() {
			document.location = "index-cloudmade.php";
		});
	</script>
</html>
