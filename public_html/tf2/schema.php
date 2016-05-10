<?php

require_once("../AutoLoad.php");

$APIkey = 'EB216E998E51A53208F84E96015A0605';

$steam = new Steam($APIkey);

$schema = $steam->getSchema();

if($schema !== NULL) {
	$i = 0;
	foreach ( $schema['items'] as $index => $item ) {
		$output = "";
		$style  = "";
		if ( $i ++ == 6 ) {
			$output .= "<div><br /></div>";
		} else {
			$i = 0;
		}
		$output .= "<div style='width:200px;height:200px;float:left;border: solid 1px #000000;'>\r\n";
		$output .= $item['name'] . " (" . $item['defindex'] . ") <br />\r\n";
		$output .= "<img src='" . $item['image_url'] . "' /><br />\r\n";
		$output .= "</div>\r\n";
		echo $output;
	}
} else {
	echo $steam->getlastCurlResponse();
}

?>
