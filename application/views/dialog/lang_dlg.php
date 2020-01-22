<!-- Modal -->
<style>
	#langModal .input-group { padding: 2px; }
	#langModal .input-group .form-control { height: auto; min-height: 34px; }
	#langModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
	#langModal input.form-control { font-size: 12px;}
	#langModal select.form-control {  }

#langModal #alert_panel .alert {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 22px;
  padding: 5px 17px;
}
</style>
<div class="modal fade" id="langModal" tabindex="-1" role="dialog" aria-labelledby="langModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="langModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="input-group ">
			        <span class="input-group-addon">Lang. Name</span>
					<input type="text" name="langname" class="form-control" />
		        </div>
				<div class="input-group">
			        <span class="input-group-addon">Default</span>
					<select class="form-control" name="default">
						<option value=""></option>
						<option value="Master Language">Master Language</option>
					</select>		        
				</div>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="lang_action_exe">OK</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	var tr_in_edit;
	
	$('#langModal').on('show.bs.modal', function (ev) 
	{
		$(this).find("#alert_panel").empty();
		
		var btn = ev.relatedTarget;
		
		if($(btn).html() == 'ADD NEW')
		{
			$(this).find(".modal-title").html('Create A New Language');
			$(this).find("input[name='langname']").val('');
			$(this).find("select[name='default']").val('');
		}
		else
		{
			$(this).find(".modal-title").html('Edit Language');
			var parents = $(btn).parents('tr');
			tr_in_edit = parents.first();
			$(this).find("input[name='langname']").val($(tr_in_edit).find('td:eq(1)').html());
			$(this).find("select[name='default']").val($(tr_in_edit).find('td:eq(2)').html());
		}
	});

	$("#langModal #lang_action_exe").click(function()
	{
		$("#langModal #alert_panel").empty();
		
		if($("#langModal input[name='langname']").val().trim() == "")
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
        	$("#langModal #alert_panel").append(alert);
            alert.append("Empty language name is not allowed.");
			return;
		}

		// TO DO : Mail address checking
		$("#langModal").modal('hide');
		var tr;
		
		if($("#langModal .modal-title").html() == 'Edit Language')
		{
			tr = $(tr_in_edit);
		}
		else
		{
			tr = $('<tr langid_db="">' + 
						'<td></td>' + 		
						'<td></td>' + 	
						'<td class="default"></td>' + 	
						'<td></td>' + 	
						'<td>' + 
							'<div class="dropdown">' + 
								'<button data-toggle="dropdown" type="button" class="btn dropdown-toggle">' + 
									 'Do <span class="caret"></span>' + 
								'</button>' + 
								'<ul role="menu" class="dropdown-menu dropdown-menu-right">' + 
							    	'<li><a data-target="#langModal" data-toggle="modal" href="#" tabindex="-1">Edit</a></li>' + 
							    	'<li><a href="#" tabindex="-1" class="lang_delete_btn">Delete</a></li>' + 
							  	'</ul>' + 
							'</div>' + 
						'</td>' + 
					'</tr>');

			$("#global_languages .basic_table tbody").append(tr);
		}

		if($("#langModal select[name='default']").val() != "")
		{
			$("#global_languages .basic_table tbody tr td.default").empty();
		}

		$(tr).find('td:eq(1)').html($("#langModal input[name='langname']").val());
		$(tr).find('td:eq(2)').html($("#langModal select[name='default']").val());

		autoNumbering();
	});
});
</script>