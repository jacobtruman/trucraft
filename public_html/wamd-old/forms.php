<style>
	body { font-size: 62.5%; }
	label, input { display:block; }
	input.text { margin-bottom:12px; width:95%; padding: .4em; }
	fieldset { padding:0; border:0; margin-top:25px; }
	h1 { font-size: 1.2em; margin: .6em 0; }
	div#users-contain { width: 350px; margin: 20px 0; }
	div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
	div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	.ui-dialog .ui-state-error { padding: .3em; }
	.validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>
<script>
	$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );

		$( "#start_date" ).datepicker();
		$( "#end_date" ).datepicker();
		
		var cache = {},
			lastXhr;
		$( "#client_id" ).autocomplete({
			minLength: 1,
			source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}

				lastXhr = $.getJSON( "ajax/clients.php", request, function( data, status, xhr ) {
					cache[ term ] = data;
					if ( xhr === lastXhr ) {
						response( data );
					}
				});
			}
		});
		
		var inputs = $('#search_form :input');

		// not sure if you wanted this, but I thought I'd add it.
		// get an associative array of just the values.
		var allFields = $([]);
		inputs.each(function() {
			//values[this] = $(this).val();
			//console.log("Adding field: "+this.id);
			allFields = allFields.add($(this));
		});
		
		var tips = $(".validateTips");
		
		//console.log(allFields);

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		$( "#search-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				"Search": function() {
					var bValid = true;

					if ( bValid ) {
						console.log("Loading search results");
						var params = "";
						for(var i = 0; i < allFields.length; i++)
						{
							if(allFields[i].value.length > 0)
							{
								console.log(allFields[i].id+" = "+allFields[i].value);
								if(params.length != 0)
									params += "&"
								params += allFields[i].id+"="+allFields[i].value;
							}
						}
						console.log("map.php?"+params);
						$( '#map' ).load("map.php?"+params);
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		$( "#settings-form" ).dialog({
			autoOpen: false,
			height: 200,
			width: 300,
			modal: true,
			buttons: {
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$("#search")
			.click(function(){
				console.log("Search clicked");
				$( "#search-form" ).dialog( "open" );
			});
			
		$("#settings")
			.click(function(){
				$( "#settings-form" ).dialog( "open" );
			});
	});
</script>

<div id="search-form" title="Search options">
	<form id="search_form">
		<fieldset>
			<label for="client_id">Client ID</label>
			<input type="text" name="client_id" id="client_id" class="text ui-widget-content ui-corner-all" />
			<label for="start_date">Start Date</label>
			<input type="text" name="start_date" id="start_date" class="text ui-widget-content ui-corner-all" />
			<label for="end_date">End Date</label>
			<input type="text" name="end_date" id="end_date" class="text ui-widget-content ui-corner-all" />
		</fieldset>
	</form>
</div>

<div id="settings-form" title="Settings">
	Coming soon
</div>