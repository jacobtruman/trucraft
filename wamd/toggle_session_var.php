<?php

if(isset($_REQUEST) && !empty($_REQUEST))
{
	foreach($_REQUEST as $var=>$val)
		$_SESSION[$var] = $val;
}
?>
