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

$api_key = "AIzaSyBJABwkkuOtokkRw4gDBQZocYz4UL-O2k8";

?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			html { height: 100% }
			body { height: 100%; margin: 0; padding: 0 }
			#map_canvas { width:100%; height: 100% }

			.phoney {
				background: -webkit-gradient(linear,left top,left bottom,color-stop(0, rgb(112,112,112)),color-stop(0.51, rgb(94,94,94)),color-stop(0.52, rgb(57,57,57)));
				background: -moz-linear-gradient(center top,rgb(112,112,112) 0%,rgb(94,94,94) 51%,rgb(57,57,57) 52%);
			}

			.phoneytext {
				text-shadow: 0 -1px 0 #000;
				color: #fff;
				font-family: Helvetica Neue, Helvetica, arial;
				font-size: 18px;
				line-height: 25px;
				padding: 4px 45px 4px 15px;
				font-weight: bold;
				/*background: url(http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/images/arrow.png) 95% 50% no-repeat;*/
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
			
			body { font-size: 62.5%; }
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
		h1 { font-size: 1.2em; margin: .6em 0; }
		div#users-contain { width: 350px; margin: 20px 0; }
		div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
		div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
		</style>
		<link href="css/jquery.loadmask.css" rel="stylesheet" type="text/css" />
		<link href="css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />

		<!--<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?=$api_key?>&sensor=false"></script>-->
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.ui.map.min.js"></script>
		<script type="text/javascript" src="js/jquery.loadmask.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.19.custom.min.js"></script>
		
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
			var pinShadow = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
				new google.maps.Size(40, 37),
				new google.maps.Point(0, 0),
				new google.maps.Point(12, 35));
			var infoBubbles = [];
			var markers = [];
			var map;
			var marker_color;
			var clients_array = [];

			function initialize()
			{
				map = new google.maps.Map(document.getElementById('map_canvas'), {
					zoom: 20,
					center: new google.maps.LatLng(0, 0),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				loadMarkers();
			}

			function loadMarkers()
			{
				latLang = new google.maps.LatLng(10, 20);
				$.getJSON( 'ajax/getCoords.php<?=$_SERVER['REQUEST_URI']?>', function(data)
				{
					clearMarkers();
					$.each( data.markers, function(i, marker)
					{
						latLang = new google.maps.LatLng(marker.latitude, marker.longitude);
						/*if(i == data.markers.length - 1)
							marker_color = "00FF00";
						else
							marker_color = marker.marker_color*/
						markers[i] = new google.maps.Marker({
							position: latLang,
							map: map,
							bounds: true,
							zIndex: i,
							icon: getPinImage(marker.marker_color, i, data.markers.length)//,
							//shadow: pinShadow
						});
						
						clients_array[marker.client.id] = marker.client;

						map.setCenter(markers[i].getPosition());
						latLang = new google.maps.LatLng(marker.latitude, marker.longitude);
						infoBubbles[i] = new InfoBubble({
							map: map,
							content: '<div class="phoneytext">'+marker.content+"<br />"+marker.client.name+'<br /><button onclick="editUser('+marker.client.id+')">edit user</button></div>',
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
							if (!infoBubbles[i].isOpen())
							{
								infoBubbles[i].open(map, markers[i]);
							}
						});
					});
				});
				setTimeout(function() { loadMarkers(); },10000);
			}

			function clearMarkers()
			{
				for(var i = 0; i < markers.length; i++)
					markers[i].setMap(null);
			}

			function getPinImage(color, num, total)
			{
				var pin_type = "d_map_xpin_letter_withshadow";
				var pin_style = "pin";
				var star_color = "00FF00";
				var font_color = "000000";
				if(num == total - 1)
				{
					pin_style = "pin_star";
				}
				//pinImage = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + color,
				pinImage = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst='+pin_type+'&chld='+pin_style+'|'+num+'|'+color+'|'+font_color+'|'+star_color,
					new google.maps.Size(21, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(10, 34));
				return pinImage;
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
						"Update": function() {
							var bValid = true;
							allFields.removeClass( "ui-state-error" );

							bValid = bValid && checkLength( name, "username", 3, 16 );
							//bValid = bValid && checkLength( email, "email", 6, 80 );
							//bValid = bValid && checkLength( password, "password", 5, 16 );

							bValid = bValid && checkRegexp( name, /^[a-z]([0-9 a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, spaces, begin with a letter." );
							// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
							//bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
							//bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

							if(bValid)
							{
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
	<body onload="initialize()">
		<div id="map_canvas"></div>
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
	</body>
	
	<script>
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
</html>
