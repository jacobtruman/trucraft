<?php
ini_set("display_errors", 1);
require_once("config.php");
require_once("AutoLoad.php");

$db = new DBConn();

$sql = "SELECT * FROM sys_office_staff";
$res = $db->query($sql);

$tabs = "";
$tabs_content = "";
while($row = $res->fetch_assoc())
{
	$tabs .= '<li><a href="#'.$row['name'].'">'.$row['name'].'</a></li>';
	$tabs_content .= '<div id="'.$row['name'].'">';
		$sql = "SELECT * FROM office_quotes WHERE author = '".$row['id']."'";
		$res2 = $db->query($sql);
		while($row2 = $res2->fetch_assoc())
		{
			$tabs_content .= $row2['quote']."<br /><br />";
		}
	$tabs_content .= '</div>';
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>TruCraft</title>
		<link type="text/css" href="css/trontastic/jquery-ui-1.8.17.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
		<script type="text/javascript">
			$(function(){

				// Accordion
				$("#accordion").accordion({ header: "h3" });
	
				// Tabs
				$('#tabs').tabs();
	

				// Dialog			
				$('#dialog').dialog({
					autoOpen: false,
					width: 600,
					buttons: {
						"Ok": function() { 
							$(this).dialog("close"); 
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
				// Dialog Link
				$('#dialog_link').click(function(){
					$('#dialog').dialog('open');
					return false;
				});

				// Datepicker
				$('#datepicker').datepicker({
					inline: true
				});
				
				// Slider
				$('#slider').slider({
					range: true,
					values: [17, 67]
				});
				
				// Progressbar
				$("#progressbar").progressbar({
					value: 20 
				});
				
				//hover states on the static widgets
				$('#dialog_link, ul#icons li').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
			});
		</script>
		<style type="text/css">
			/*demo page css*/
			body{ font: 62.5% "Trebuchet MS", sans-serif; margin: 50px;}
			.demoHeaders { margin-top: 2em; }
			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		</style>	
	</head>
	<body>		
		<h2 class="demoHeaders">TruCraft logo placeholder</h2>
		<div id="tabs">
			<ul>
				<?
					echo $tabs;
				?>
			</ul>
			<?
				echo $tabs_content;
			?>
		</div>

	</body>
</html>


