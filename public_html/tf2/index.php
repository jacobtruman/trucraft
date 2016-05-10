<?php

ini_set('display_errors', 1);

require_once("../AutoLoad.php");

$APIkey = 'EB216E998E51A53208F84E96015A0605'; // Use your own API key here
$profile = '76561198022319482'; // Find some profile number and place it here

$steam = new Steam($APIkey);
$steam->getSteamProfile($_REQUEST['id']);

if($steam->steamProfile)
{
	echo $steam->steamProfile['steamID']."<br /><img src='".$steam->steamProfile['avatarFull']."' /><br /><br />";

	// get list of all items
	$schema = $steam->getSchema();
	foreach($schema as $key=>$val)
	{
		$itemSchema[$key] = $val;
	}

	foreach($itemSchema['items'] as $id=>$item)
	{
		$newAllItems[$item['defindex']] = $item;
	}
	$itemSchema['items'] = $newAllItems;
	if(isset($_REQUEST['dump_items']) && $_REQUEST['dump_items']) dump($itemSchema['items']);

	$final_list = array();

	// Download and decode the json file
	$userItems = $steam->getPlayerItems($steam->steam_id);

	if($userItems['status'] !== 1) {
		echo $userItems['statusDetail']."<br />";
	} else {
		$items = $userItems['items'];

		// Iterate through each item
		foreach ( $items as $ind => $item ) {
			// Store all of the item's information in separate variables
			if ( isset( $_REQUEST['dump_item'] ) && $_REQUEST['dump_item'] ) {
				dump( $itemSchema['items'][ $item['defindex'] ] );
			}
			$defindex = $nitem['defindex'] = $item['defindex'];
			$nitem['id'] = $item['id'];
			if ( ! isset( $final_list[ $defindex . "_" . $item['quality'] ] ) ) {
				$nitem['quantity'] = $item['quantity'];
			} else {
				$nitem['quantity'] = $final_list[ $defindex . "_" . $item['quality'] ]['quantity'] + 1;
			}
			$nitem['img']  = $itemSchema['items'][ $defindex ]['image_url'];
			$nitem['name'] = $itemSchema['items'][ $defindex ]['name'];

			if ( strstr( $nitem['name'], "Supply Crate" ) ) {
				$find          = search_array( "defindex", $item['attributes'][0]['defindex'], $itemSchema['attributes'] );
				$nitem['name'] = ucwords( $itemSchema['attributes'][ $find ]['name'] ) . " " . $item['attributes'][0]['float_value'];
			}
			$nitem['level']             = $item['level'];
			$nitem['flag_cannot_trade'] = ( array_key_exists( 'flag_cannot_trade', $item ) ? $item['flag_cannot_trade'] : false );
			$nitem['inventory']         = $item['inventory']; // The inventory value is stored just like all of the others
			$nitem['position']          = $nitem['inventory'] & 65535; // You can directly perform bitwise operations on the value. PHP understands that you mean this to be a number
			$nitem['equipped']          = ( $nitem['inventory'] >> 16 ) & 1023; // More bitwise operations to get the equipped number
			#$nitem['equippedString'] = getEquipped($nitem['equipped']); // Convert the equipped number into a string
			$nitem['quality'] = $item['quality'];
			$nitem['old_ids'] = array();
			if(isset($final_list[ $defindex . "_" . $item['quality'] ]['old_ids'])) {
				$nitem['old_ids'] = $final_list[ $defindex . "_" . $item['quality'] ]['old_ids'];
			}
			$nitem['old_ids'][] = $item['original_id'];
			$nitem['ids'] = array();
			if(isset($final_list[ $defindex . "_" . $item['quality'] ]['ids'])) {
				$nitem['ids'] = $final_list[ $defindex . "_" . $item['quality'] ]['ids'];
			}
			$nitem['ids'][] = $item['id'];
			$final_list[ $defindex . "_" . $item['quality'] ] = $nitem;

			$db  = new DBConn();
			$sql = "INSERT INTO `tf2_items` SET
				`id` = '" . $db->real_escape_string( $item['id'] ) . "',
				`quality` = '" . $db->real_escape_string( $item['quality'] ) . "',
				`date_added` = NOW(),
				`last_updated` = NOW(),
				`name` = '" . $db->real_escape_string( $nitem['name'] ) . "',
				`user_id` = '" . $db->real_escape_string( $steam->steam_id ) . "'";
			$sql .= "ON DUPLICATE KEY UPDATE last_updated = NOW()";
			#dump($sql);
			$db->query( $sql );
		}
	}

	$i = 0;
	foreach($final_list as $index=>$item)
	{
		$index = explode("_", $index);
		$defindex = $index[0];
		$quality = ucwords(array_search($item['quality'], $itemSchema['qualities']));
		$output = "";
		$style = "";
		if($i++ == 6)
			$output .= "<div><br /></div>";
		else
			$i = 0;
		$output .= "<div style='width:200px;height:200px;float:left;border: solid 1px #000000;'>\r\n";
		$output .= ($quality != "Unique" ? $quality." " : "").$item['name']." (".$item['defindex'].")<br />\r\n";
		$output .= "<img src='".$item['img']."' />x".$item['quantity']."<br />\r\n";
		$output .= implode(", ", $item['ids'])."<br />\r\n";
		$output .= implode(", ", $item['old_ids'])."<br />\r\n";
		$output .= "</div>\r\n";
		echo $output;
	}
}
	
	function dump($x)
	{
		echo "<pre>";
		print_r($x);
		echo "</pre>";
	}

	function getEquipped($equipNumber) {
		// Create an array with all of the classes in the proper order
		$classList = array(0=>'Scout', 'Sniper', 'Soldier', 'Demoman', 'Medic', 'Heavy', 'Pyro', 'Spy', 'Engineer');
		// Start with an empty string
		$equippedString = '';
		for ($i = 0; $i < 10; $i++) {
			if ((1<<$i) & $equipNumber) { // Check that the class's bit appears in the number
				if ($equippedString)
					$equippedString .= ', '; // If the string is not empty, add a comma
					
				$equippedString .= $classList[$i]; // Add the name of the class to the string if it is equipped by that class
			}
		}
		if (!$equippedString)
			$equippedString = 'None'; // The string is "None" if no one has it equipped
		return $equippedString; 
	}

	function search_array($skey, $sval, $arr)
	{
		foreach($arr as $key=>$val)
		{
			if($val[$skey] == $sval)
			{
				return $key;
				break;
			}
		}
	}
?>
