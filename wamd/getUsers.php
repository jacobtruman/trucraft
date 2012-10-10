<?php
//ini_set('display_errors', 1);
require_once("../AutoLoad.php");

$coords = new Coords();

echo json_encode(array("markers"=>$coords->getCoords($_REQUEST)));

?>