<?
// login page

/*

TODO: create login page
TODO: add login "catch" to each page
TODO: have a session timeout contained in a variable

*/

?>

<html>
	<head>
		<link href="css/main.css" rel="stylesheet" type="text/css" />
		<link href="css/login.css" rel="stylesheet" type="text/css" />
		<link href="css/jquery.loadmask.css" rel="stylesheet" type="text/css" />
		<link href="css/dot-luv/jquery-ui-1.8.19.custom.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.19.custom.min.js"></script>
		<script type="text/javascript" src="jsForms/login.js"></script>
	</head>

	<body>
		<div id="loginFormDiv" title="Login">
			<form id="loginForm">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" />
				<label for="password">Password</label>
				<input type="password" name="password" id="password" class="text ui-widget-content ui-corner-all" />
			</form>
		</div>
	</body>
</html>
