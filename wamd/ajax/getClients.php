<?php
//ini_set('display_errors', 1);
require_once("../AutoLoad.php");

$clients = new Clients($_SESSION['group']);

echo json_encode(array("clients"=>$clients->getClientArray()));

?>