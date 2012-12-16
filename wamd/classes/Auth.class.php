<?

class Auth
{
	private $_admin = array();
	private $_auth = false;
	
	private $_header = array("id", "username", "password", "ip", "user_agent");
	
	public function __construct($auth_id)
	{
		$auth_obj = explode("_", base64_decode($auth_id));
		if(count($auth_obj) == count($this->_header))
		{
			$auth_obj = array_combine($this->_header, $auth_obj);

			$this->_db = new DBConn("wamd");

			$sql = "SELECT * FROM admin WHERE id = '".$auth_obj['id']."' AND username = '".$auth_obj['username']."' AND password = '".$auth_obj['password']."' AND active = 1";
			$res = $this->_db->query($sql);

			if($res->num_rows)
			{
				$this->_admin = $res->fetch_assoc();
				$this->_auth = true;
			}
		}
	}

	public function __get($name)
	{
		if(!isset($this->_admin[$name]))
			return false;
		return $this->_admin[$name];
	}
	
	public function __isset($name)
	{
		return isset($this->_admin[$name]);
	}
	
	public function isAuthenticated()
	{
		return $this->_auth;
	}

	static public function login($params)
	{
		if(isset($params['username']) && isset($params['password']))
		{
			$db = new DBConn("wamd");

			$sql = "SELECT * FROM admin WHERE username = '".$params['username']."' AND password = '".md5($params['password'])."' AND active = 1";
			
			$res = $db->query($sql);
			
			if($res->num_rows)
			{
				$row = $res->fetch_assoc();
				$auth_code = base64_encode($row['id']."_".$row['username']."_".$row['password']."_".$_SERVER['REMOTE_ADDR']."_".str_replace("_", " ", $_SERVER['HTTP_USER_AGENT']));
				$auth = array("auth_id"=>$auth_code);
				return json_encode($auth);
			}
			else
			{
				return false;
			}
		}
	}
}

?>
