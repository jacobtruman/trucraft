<?php
set_time_limit(60);

$app_id = "103040519777367";
$secret = "1f17e2227914f24a7a8c8c61d233d08d";

$upload = false;

require_once("AutoLoad.php");
if(isset($_GET['passed']))
{
	$title = "Oops, something went wrong";
	$content = "";
	if(isset($_REQUEST['access_token']))
	{
		$company = isset($_REQUEST['company']) ? base64_decode($_REQUEST['company']) : "";
		$fpid = isset($_REQUEST['fpid']) ? base64_decode($_REQUEST['fpid']) : "";
		$access_token = $_REQUEST['access_token'];
		$content .= "User Access Token: ".$access_token;
		if(!empty($company))
			$content .= "<br />Company: ".$company;
		if(!empty($fpid))
			$content .= "<br />Fanpage ID: ".$fpid;

		$fb_config = array('appId'  => $app_id, 'secret' => $secret);
		$facebook = new Facebook($fb_config);
		//$url = $facebook->getLoginUrl(array("scope"=>"read_insights,offline_access,manage_pages"));
		
		if(validToken($fpid, $access_token))
		{
			$title = "Thank You";

			$url = "https://graph.facebook.com/me/accounts?access_token=".$access_token;

			$ret = json_decode(curl_get_file_contents($url), true);
			
			// create file to upload
			$filename = $company."_".$fpid.".txt";//."_".date("Y-m-d").".txt";
			$file = dirname(__FILE__)."/".$filename;
			$content .= "<br />Upload file: ".$file;
			
			if(isset($ret['data'][0]['access_token']) && !empty($ret['data'][0]['access_token']))
			{
				$page_access_token = $ret['data'][0]['access_token'];
				$content .= "<br />Page Access Token: ".$page_access_token;
				file_put_contents($file, $page_access_token);
			}
			else
			{
				file_put_contents($file, $access_token);
			}
			
			if($upload)
			{
				// upload file to both data centers
				$host1 = "ftp2.omniture.com";
				$host2 = "ftp.omniture.com";
				$username = "fbtoken_update";
				$password = "fbtoken_pass";

				// set up basic connection
				$conn1 = ftp_connect($host1);
				$conn2 = ftp_connect($host2);

				// login with username and password
				$login_result = ftp_login($conn1, $username, $password);
				ftp_pasv($conn1, true);
				
				$login_result = ftp_login($conn2, $username, $password);
				ftp_pasv($conn2, true);

				// upload a file
				if(ftp_put($conn1, "./".$filename, $file, FTP_BINARY) && ftp_put($conn2, "./".$filename, $file, FTP_BINARY))
					$content .= "<br />Your new token has been successfully uploaded.";
				else
					$content .= "<br />Oops, the access token was not uploaded.";

				// close the connection
				ftp_close($conn1);
				ftp_close($conn2);
			}
			unlink($file);
		}
		else
		{
			$content .= "<br />The access_token is not valid for Fanpage ID ".$fpid.". You may not be logged into facebook with an account with admin rights to the Fanpage.";
		}
	}
	else
		$content .= "<br />No access_token was returned.";

	echo "<html>
<head>
	<title>Thank You</title>
</head>
<body>
<h1>".$title."</h1>
</body>
</html>
<pre>".$content."</pre>
</body>
</html>";
}
else
{
$params = base64_decode($_REQUEST['params']);
echo "<script>
	var str = String(window.location);
	var index = str.indexOf(\"access_token\");
	var length = str.length;
	window.location = '".$_SERVER['PHP_SELF']."?passed=1&'+str.substr(index,length - index)+'".(!empty($params) ? "&".$params : "")."';
</script>";

}

function validToken($fp_id, $access_token)
{
	$insights = array();

	$path = $fp_id . '/insights';
	$url = FacebookMethods::getGraphApiUrl($path);

	$params = array();
	$params['access_token'] = $access_token;
	$params['method'] = 'GET';
	$params['since'] = date("Y-m-d", strtotime("-1 week"));

	$insights = FacebookMethods::fetchUrl($url, $params);
	
	if(isset($insights['data']) && count($insights['data']) > 4)
	{
		return true;
	}
	return false;
}

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