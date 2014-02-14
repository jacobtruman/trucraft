<?php
ini_set("display_errors", 1);

// TODO change these to your API key and secret
define("API_CONSUMER_KEY", "vsgzzlv8diwr");
define("API_CONSUMER_SECRET", "P49Zbw5yrsO7j0TO");

// create a new instance of the OAuth PECL extension class
$oauth = new OAuth(API_CONSUMER_KEY, API_CONSUMER_SECRET);
 
// get our request token
$api_url = "https://api.linkedin.com/uas/oauth/requestToken";
$rt_info = $oauth->getRequestToken($api_url);
 
// now set the token so we can get our access token
$oauth->setToken($rt_info["oauth_token"], $rt_info["oauth_token_secret"]);

setAuthToken($oauth);

$api_url = "http://api.linkedin.com/v1/people/~";
$oauth->fetch($api_url, null, OAUTH_HTTP_METHOD_GET, array('x-li-format' => 'json'));
$output = $oauth->getLastResponse();
print_r($output);

function setAuthToken(&$oauth)
{
	$at_info = array("oauth_token"=>"00649447-efce-4bb4-9c42-b3b436301dec", "oauth_token_secret"=>"02a117b5-491c-4c04-9075-5ada3cfeb74e");

	if(empty($at_info))
	{
		// instruct on how to authorize the app
		print("Please visit this URL:\n\n");
		printf("https://www.linkedin.com/uas/oauth/authenticate?oauth_token=%s", $rt_info["oauth_token"]);
		print("\n\nIn your browser and then input the numerical code you are provided here: ");

		// ask for the pin  
		$pin = trim(fgets(STDIN));

		// get the access token now that we have the verifier pin
		$at_info = $oauth->getAccessToken("https://api.linkedin.com/uas/oauth/accessToken", "", $pin);
	}
	// set the access token so we can make authenticated requests
	$oauth->setToken($at_info["oauth_token"], $at_info["oauth_token_secret"]);
}

?>
