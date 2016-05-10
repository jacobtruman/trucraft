<?php

require_once("AutoLoad.php");
$gv = new GoogleVoice("jacob.truman", '$J1n54n3');
$gv->sms("8018574316", "TextMsg");
echo $gv->status;

?>
