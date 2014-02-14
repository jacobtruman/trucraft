<?php // a super-stripped down 2-leg oauth server/client example

//http://oauth.net/code/
//http://oauth.googlecode.com/svn/code/php/OAuth.php
require 'oauth.class.php';
 
$key = 'key';
$secret = 'secret';
$consumer = new OAuthConsumer($key, $secret);
$sig_method = new OAuthSignatureMethod_HMAC_SHA1;
 
if($_GET['server']){
    
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $sig = $_GET['oauth_signature'];    
    $req = new OAuthRequest($method, $uri);
    
    //token is null because we're doing 2-leg
    $valid = $sig_method->check_signature( $req, $consumer, null, $sig );
    
    if(!$valid){
        die('invalid sig');
    }
    echo 'orale!';
}else{
    
    //call this file
    $api_endpoint = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
    
    //handle request in 'server' block above
    $parameters = array('server'=>'true');
 
    //use oauth lib to sign request
    $req = OAuthRequest::from_consumer_and_token($consumer, null, "GET", $api_endpoint, $parameters);
    $sig_method = new OAuthSignatureMethod_HMAC_SHA1();
    $req->sign_request($sig_method, $consumer, null);//note: double entry of token
 
    //get data using signed url
    $ch = curl_init($req->to_url());
    curl_exec($ch);
    curl_close($ch);  
}

?>