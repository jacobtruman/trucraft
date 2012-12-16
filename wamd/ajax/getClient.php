<?php
//ini_set('display_errors', 1);
require_once("../AutoLoad.php");

$client = new Client($_REQUEST['client_id']);

echo json_encode(array("client"=>$client->getClientArray()));

?>