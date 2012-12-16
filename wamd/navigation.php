<?
// navigation page

$menu_items = array("Home", "Settings", "Help", "Users");

?>

<div id="menu">
	<div id="menu-bg">
	<?
		foreach($menu_items as $item)
		{
			echo "<div id='".strtolower($item)."'>
				<div class='menu-image'>&nbsp;</div>
				<div class='menu-text'>".$item."</div>
				<div class='clear'></div>
			</div>\n";
		}
	?>
	<div id='hide'><img src="images/cog_delete.png" /></div>
	</div>
</div>
<div id="menu-tab">
	<img src="images/cog_add.png" />
</div>

<div id="usersForm" title="Users"></div>

<script>

	$( "#usersForm" ).dialog({
		autoOpen: false,
		height: window.innerHeight * .9,
		width: window.innerWidth * .9,
		modal: true,
		draggable: false,
		resizable: false,
		open: function ()
        {
            $(this).load('users.php');
        },
		close: function()
		{
			//
		}
	});

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
	
	$("#users").click(function() {
		$( "#usersForm" ).dialog( "open" );
	});
	
	$( "#rerun" ).button().click(function() {
		alert( "Running the last action" );
	});
</script>