<form id="userForm">
	<fieldset id="user_fields">
		
	</fieldset>
</form>
<script>
	$.getJSON( 'ajax/getClient.php?client_id=<?=$_REQUEST['client_id']?>', function(data)
	{
		$.each( data.client, function(field, value)
		{
			if(field == "id")
			{
				$('#user_fields').append('<input type="hidden" name="'+field+'" value="'+value+'">');
			}
			else if(field != "date_created" && field != "date_updated")
			{
				$('#user_fields').append('<label for="'+field+'">'+field+'</label>');
				$('#user_fields').append('<input type="text" name="'+field+'" id="'+field+'" class="text ui-widget-content ui-corner-all" />');
				$('#'+field).val(value);	
			}
		});
	});
</script>