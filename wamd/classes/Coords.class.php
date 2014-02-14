<?
ini_set("display_errors", 1);
class Coords
{
	private $_db;
	private $_clients = array();

	public function __construct() {
		$this->_db = new DBConn("wamd");
	}
	
	public function getCoords($params) {
		$this->_params = $params;
		$this->_buildQueryParams();
		
		$sql = "SELECT * FROM coords AS co, clients AS cl WHERE ".implode(" AND ", $this->_query_params)." AND co.client_id = cl.client_id > 0 ORDER BY location_time";

		$res = $this->_db->query($sql);

		$coords = array();
		while($row = $res->fetch_assoc()) {
			$coord['latitude'] = $row['lat'];
			$coord['longitude'] = $row['lon'];
			$coord['title'] = $row['location_time'];
			$coord['client_id'] = $row['client_id'];
			if(!in_array($row['client_id'], $this->_clients))
				$this->_clients[$row['client_id']] = $this->_getClient($row['client_id']);
			$coord['marker_color'] = $this->_clients[$row['client_id']]->marker_color;
			$coord['content'] = $row['location_time']."<br />".$row['client_id'];
			$coord['provider'] = $row['provider'];
			$coord['client'] = $this->_clients[$row['client_id']]->getClientArray();
			$coords[] = $coord;
		}
		return $coords;
	}
	
	private function _buildQueryParams() {
		if(isset($this->_params['start_date']) && isset($this->_params['end_date']))
			$this->_query_params[] = "co.location_time BETWEEN '".date("Y-m-d H:i:s", strtotime($this->_params['start_date']))."' AND '".date("Y-m-d H:i:s", strtotime($this->_params['end_date']))."'";
		else if(isset($this->_params['start_date']))
			$this->_query_params[] = "location_time >= '".date("Y-m-d H:i:s", strtotime($this->_params['start_date']))."'";
		else if(isset($this->_params['lookback']))
			$this->_query_params[] = "co.location_time > '".date("Y-m-d H:i:s", strtotime("-".$this->_params['lookback']." hour"))."'";
		else
			$this->_query_params[] = "co.location_time > '".date("Y-m-d H:i:s", strtotime("-1 hour"))."'";

		if(isset($this->_params['client_id']))
			$this->_query_params[] = "co.client_id = '".$this->_params['client_id']."'";

		if(isset($this->_params['group']))
			$this->_query_params[] = "cl.group = '".$this->_params['group']."'";
		else if(isset($_SESSION['group']))
			$this->_query_params[] = "cl.group = '".$_SESSION['group']."'";
		else
			$this->_query_params[] = "cl.group = '1'";

		if(isset($this->_params['provider'])) {
			$this->_query_params[] = "provider = '".$this->_params['provider']."'";
		}
	}

	private function _getClient($client_id = 0) {
		$client = false;
		if(!empty($client_id))
			$client = new Client($client_id);
		return $client;
	}
	
	public function addCoordsOLD($request) {
		$db_fields = array("client_id", "lat", "lon", "provider", "location_time", "location_timezone", "battery", "accuracy", "speed", "altitude", "bearing", "charging", "charging_how");

		$params = array();

		foreach($db_fields as $field) {
			if(isset($request[$field])) {
				$params[$field] = $request[$field];
			}
		}

		if(!isset($params['lat']) || !isset($params['lat']) && isset($request['coords'])) {
			$coords = explode("_", $request['coords']);
			$params['lat'] = (isset($request['lat']) ? $request['lat'] : $coords[0]);
			$params['lon'] = (isset($request['lon']) ? $request['lon'] : $coords[1]);
		}

		$sql = "INSERT INTO coords SET ";
		foreach($params as $key=>$val) {
			$sql .= $key." = '".$val."',";
		}
		$sql .= "date = NOW()";

		$this->_db->query($sql);

		return "SUCCESS";	
	}
	
	public function addCoords($chunk) {
		
	}
}

?>
