$(function()
{
	// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
	$( "#dialog:ui-dialog" ).dialog( "destroy" );

	var username = $("#username"),
		password = $("#password"),
		allFields = $([]).add(username).add(password),
		tips = $( ".validateTips" );

	function updateTips( t ) {
		tips
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tips.removeClass( "ui-state-highlight", 1500 );
		}, 500 );
	}

	function checkLength( o, n, min, max ) {
		if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			updateTips( "Length of " + n + " must be between " +
				min + " and " + max + "." );
			return false;
		} else {
			return true;
		}
	}

	function checkRegexp( o, regexp, n ) {
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			updateTips( n );
			return false;
		} else {
			return true;
		}
	}

	$( "#loginFormDiv" ).dialog({
		autoOpen: true,
		height: 225,
		width: 300,
		//modal: true,
		draggable: false,
		resizable: false,
		buttons: {
			"Login": function()
			{
				var bValid = true;
				allFields.removeClass("ui-state-error");
				
				bValid = bValid && checkLength($('#username'), "username", 3, 16);
				bValid = bValid && checkLength($('#password'), "password", 5, 16);

				// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
				bValid = bValid && checkRegexp($('#username'), /^[a-z]([0-9 a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, spaces, begin with a letter." );
				bValid = bValid && checkRegexp($('#password'), /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

				if(bValid)
				{
					var $inputs = $('#loginForm :input');
					var userArray = {};
					$inputs.each(function()
					{
						userArray[this.name] = $(this).val();
					});
					$.ajax({
						type: "POST",
						url: "ajax/login.php",
						data: userArray
					}).done(function(msg)
					{
						if(msg == "SUCCESS")
						{
							console.log(msg);
							$(location).attr('href',"index.php");
						}
						else
						{
							console.log(msg);
						}
					});
				}
			}
		},
		close: function()
		{
			$("#loginFormDiv").dialog("open");
		}
	});

});
