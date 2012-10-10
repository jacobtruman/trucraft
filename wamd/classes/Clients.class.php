<?

class Clients
{
	private $_db;
	private $_clients = array();

	public function __construct($client_id)
	{
		$this->_db = new DBConn("wamd");
	}

	public function __get($name)
	{
		if(!isset($this->_client[$name]))
			return false;
		return $this->_client[$name];
	}
	
	public function getClientArray()
	{
		$sql = "SELECT * FROM clients";

		$res = $this->_db->query($sql);
		
		while($row = $res->fetch_assoc())
		{
			$this->_clients[] = $row;
		}
		return $this->_clients;
	}
}

?>