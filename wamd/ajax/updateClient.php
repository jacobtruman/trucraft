<?php
//ini_set('display_errors', 1);
require_once("../AutoLoad.php");

if(isset($_REQUEST['client_id']))
{
	$client = new Client($_REQUEST['client_id']);

	if(isset($_REQUEST['name']) && !empty($_REQUEST['name']))
		$client->name = $_REQUEST['name'];
}
?>