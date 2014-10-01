<?php

$ch = curl_init("http://api.steampowered.com/IEconItems_440/GetSchema/v0001/?key=EB216E998E51A53208F84E96015A0605");

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($ch);
curl_close($ch);

$contents = json_decode($result, true);

$i = 0;
foreach($contents['result']['items'] as $index=>$item) {
	$output = "";
	$style = "";
	if($i++ == 6)
		$output .= "<div><br /></div>";
	else
		$i = 0;
	$output .= "<div style='width:200px;height:200px;float:left;border: solid 1px #000000;'>\r\n";
	$output .= $item['name']."<br />\r\n";
	$output .= "<img src='".$item['image_url']."' /><br />\r\n";
	$output .= "</div>\r\n";
	echo $output;
}

?>
