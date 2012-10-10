<script>
	var client_id;
	var value;
	
	$.getJSON( 'ajax/getClients.php<?=$_SERVER['REQUEST_URI']?>', function(data)
	{
		$.each( data.clients, function(i, client)
		{
			value = client.client_id;
			$('#usersForm').append('<div id="div_'+value+'">');
			$('#usersForm').append(client.name);
			$('#usersForm').append('<button id="edit_'+value+'">Edit</button>');
			$('#usersForm').append('<button id="delete_'+value+'">Delete</button>');
			$('#usersForm').append('</div>');
			$('#edit_'+value).button().click(function(){ client_id=this.id.substr(this.id.indexOf("_")+1); $("#editUserForm").dialog("open"); });
			$('#delete_'+value).button().click(function(){ alert('Delete '+this.id.substr(this.id.indexOf("_")+1));});
		});
	});
</script>