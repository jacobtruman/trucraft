<?
ini_set('display_errors', 1);
require_once("../../classes/DBConn.class.php");

$db = new DBConn();

$sql = "SELECT DISTINCT(client_id) FROM coords WHERE client_id LIKE '%".$_REQUEST['term']."%' ORDER BY client_id";

$res = $db->query($sql);

$clients = array();

while($row = $res->fetch_assoc())
{
	$clients[] = array('id'=>$row['client_id'], 'label'=>$row['client_id'], 'value'=>$row['client_id']);
}


echo json_encode($clients);

?>