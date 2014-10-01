<?php

if(isset($_REQUEST['readfile'])) {
	if(fileIsAllowed($_REQUEST['readfile'])) {
		$ret_val = array("success"=>true, "contents" => file_get_contents($_REQUEST['readfile']));
	} else {
		$ret_val = array("success"=>false);
	}
} else {
	$ret_val = array("key1"=>"val1", "key2"=>"val2");
}

echo json_encode($ret_val);

function fileIsAllowed($file = NULL) {
	$allowed_files = array("/etc/hosts.deny");
	if($file !== NULL && in_array($file, $allowed_files)) {
		return true;
	}
	return false;
}

?>
