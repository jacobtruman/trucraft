<?

class Client
{
	private $_db;
	private $_client = false;

	public function __construct($client_id)
	{
		$this->_db = new DBConn("wamd");

		$sql = "SELECT * FROM clients WHERE client_id = '".$client_id."'";

		$res = $this->_db->query($sql);
		
		if($res->num_rows)
		{
			$this->_client = $res->fetch_assoc();
		}
		return $this->_client;
	}

	public function __get($name)
	{
		if(!isset($this->_client[$name]))
			return false;
		return $this->_client[$name];
	}
	
	public function __set($name, $value)
    {
		if($this->_client[$name] != $value)
		{
			$this->_client[$name] = $value;
			$sql = "UPDATE clients SET `".$name."` = '".$this->_db->real_escape_string($value)."' WHERE id = '".$this->_client['id']."'";

			$this->_db->query($sql);
		}
    }
	
	public function __isset($name)
	{
		return isset($this->_client[$name]);
	}
	
	public function getClientArray()
	{
		return $this->_client;
	}
}

?>