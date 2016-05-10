<?php

class Steam {

	protected $apiKey;
	protected $apiBaseUrl = "http://api.steampowered.com";
	protected $curlResponse = NULL;

	public $steamProfile = NULL;
	public $steam_id = NULL;

	public function __construct($apiKey) {
		$this->apiKey = $apiKey;
	}

	public function getSchema() {
		$url = $this->apiBaseUrl."/IEconItems_440/GetSchema/v0001/";
		$params = array('key' => $this->apiKey);
		$result = json_decode($this->curlUrl($url, $params), true);
		return $result['result'];
	}

	public function getPlayerItems($steamId) {
		$url = $this->apiBaseUrl."/IEconItems_440/GetPlayerItems/v0001/";
		$params = array('key' => $this->apiKey, 'SteamID' => $steamId);
		$result = json_decode($this->curlUrl($url, $params), true);
		return $result['result'];
	}

	public function getSteamProfile($id = NULL) {
		$url = "http://steamcommunity.com/id/".$id."/";
		$params = array('xml' => 1);
		$result = $this->curlUrl($url, $params);
		$this->steamProfile = json_decode(json_encode(simplexml_load_string($result, null, LIBXML_NOCDATA)), true);
		if(isset($this->steamProfile['steamID64'])) {
			$this->steam_id = $this->steamProfile['steamID64'];
		}
	}

	public function getlastCurlResponse() {
		return $this->curlResponse;
	}

	protected function curlUrl($url, $params = array()) {
		if(count($params) > 0) {
			$url .= '?' . http_build_query($params);
		}
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		$this->curlResponse = $result;
		curl_close($ch);

		return $result;
	}
}

?>