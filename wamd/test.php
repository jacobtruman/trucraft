<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
    <style>
        .loading { background: url(/img/spinner.gif) center no-repeat !important}
    </style>
</head>
<body>
    <a class="ajax" href="http://www.google.com">
      Open as dialog
    </a>

    <script type="text/javascript">
	$('a.ajax').live('click', function() {
		var url = this.href;
		var dialog = $("#dialog");
		if ($("#dialog").length == 0) {
			dialog = $('<div id="dialog" style="display:hidden"></div>').appendTo('body');
		} 

		// load remote content
		dialog.load(
				url,
				{},
				function(responseText, textStatus, XMLHttpRequest) {
					dialog.dialog();
				}
			);
		//prevent the browser to follow the link
		return false;
	});
    </script>
</body>
</html>