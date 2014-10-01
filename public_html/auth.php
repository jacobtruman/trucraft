<?php
ini_set('memory_limit', -1);
echo "<pre>";
require_once(dirname(__FILE__)."/AutoLoad.php");

$access_token = "AAABdtv2ULFcBAI2KdmmvbgCl2u7X6XX4nirtUFwXhJUdbQjrXDaEvmxbIy7yuVdXUc1MMImeNRLz0Vwq3w9Tr5VMNiRjhZCxkpdK2awZDZD";

$fb_config = array('appId'  => '103040519777367', 'secret' => '1f17e2227914f24a7a8c8c61d233d08d');

$facebook = new Facebook($fb_config);


$url = $facebook->getLoginUrl(array("scope"=>"read_insights,offline_access,manage_pages"));
var_dump($url);

$access_token = $facebook->getAccessToken();

$next_url = "https://graph.facebook.com/me/accounts?access_token=".$access_token;

$ret = curl_get_file_contents($next_url);

var_dump($ret);

//$facebook->setExtendedAccessToken();

//var_dump($facebook->getAccessToken());

echo "</pre>";

  function curl_get_file_contents($URL) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
    curl_close($c);
    if ($contents) return $contents;
    else return FALSE;
  }

?>