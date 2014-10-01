<?php

class FacebookMethods{

  private static function getUrl($httphost, $path, $params) {
    $url = $httphost;
    if ($path) {
      if ($path[0] === '/') {
        $path = substr($path, 1);
      }
      $url .= $path;
    }
    if ($params) {
      $url .= '?' . http_build_query($params);
    }
    return $url;
  }

  public static function getGraphApiUrl($path = '', $params = array()) {
    return self::getUrl('https://graph.facebook.com/', $path, $params);
  }

  public static function getRestApiUrl($params = array()) {
    return self::getUrl('https://api.facebook.com/',
                         'restserver.php', $params);
  }

  public static function fetchUrl($url, $params, $print_url = false, $fails = 0) {
    $params['format'] = 'json-strings';
    $ch = curl_init();
    $opts = array(
		CURLOPT_CONNECTTIMEOUT => 60,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_TIMEOUT => 60,
		CURLOPT_USERAGENT => 'facebook-php-2.0',
		CURLOPT_URL => $url
    );
    $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    curl_setopt_array($ch, $opts);
	$result = curl_exec($ch);
	if($print_url)
	{
		echo "\n\n".$url."?";
		foreach($params as $param=>$val)
			echo $param."=".$val."&";
		echo "\n\n";
	}
	
    if ($result === false) {
		$fails++;
		if($fails < 5)
		{
			echo "FAILED ".$fails." TIMES, TRYING AGAIN\n";
			return self::fetchUrl($url, $params, $fails);
		}
		else
		{
			$e = new CustomException(curl_error($ch)."\n\nError #:".curl_errno($ch));
			curl_close($ch);
			throw $e;
		}
    }
    curl_close($ch);
    return json_decode($result, true);
  }
}