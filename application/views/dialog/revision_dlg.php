<!-- Modal -->
<style>
#revisionModal .input-group { padding: 2px; }
#revisionModal .input-group .form-control { height: auto; min-height: 34px; }
#revisionModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
#revisionModal input.form-control { font-size: 12px;}
#revisionModal ul.dropdown-menu { max-height: 200px; min-width: 50px; overflow: auto; }
#revisionModal ul.dropdown-menu li a { font-size: 12px; }

#revisionModal #alert_panel .alert {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 22px;
  padding: 5px 17px;
}
</style>
<div class="modal fade" id="revisionModal" tabindex="-1" role="dialog" aria-labelledby="revisionModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="revisionModalLabel">New Revision</h4>
			</div>
			<div class="modal-body">
				<div class="input-group ">
			        <span class="input-group-addon">Revision Name</span>
					<input type="text" name="revisionname" class="form-control" />
		        </div>
		        <div class="panel panel-default" style="margin: 10px 2px 0;">
				  <div class="panel-heading">
				    <h3 class="panel-title" style="font-size: 12px;">Creating Revision Note</h3>
				  </div>
				  <div class="panel-body" style="font-size: 11px;">
				    Please type in name of new revision.<br />
				    This revision name should be met following condition:<br /> 
				    1. Should have 4 - 12 alphabetic and numeric characters and '_'.<br />
				    2. Should not be duplicated with existing other revisions.<br />
				    <br />
				    After creating revision, this control panel will be switched to created revision automatically.<br />
				    Then you can update the settings of this newly create revision at REV - (Newly Created Revision) menu
				  </div>
				</div>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="revision_action_exe">OK</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	var tr_in_edit;
	
	$('#revisionModal').on('show.bs.modal', function (ev) 
	{
		$(this).find("#alert_panel").empty();
		$(this).find("input[name='revisionname']").val('');
	});

	$("#revisionModal #revision_action_exe").click(function()
	{
		var revision_name = $("#revisionModal input[name='revisionname']").val().trim();
		
		if(revision_name.length < 4 || revision_name.length > 12 )
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
            alert.append("Revision name length should be 4 ~ 12.");
        	$("#revisionModal #alert_panel").append(alert);
            return;
		}

		$.ajax
		({
	        type: "post",
	        cache: false,
	        url: site_url + "revision/createNewRevision",
	        dataType: "json",
	        data: { revision_name : revision_name },
	        success: function (res) 
	        {
	        	var alert = $('<div class="alert" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
	        	
	            if(res.errors.length == 0)
	            {
	            	alert.addClass("alert-success").append("A new revision created successfully.<br />Now switching to newly created revision.");
		        	$("#revisionModal #alert_panel").append(alert);
		        	
					var revision_id = res.rev_id;
					
	        		$.ajax
	        		({
	        	        type: "post",
	        	        cache: false,
	        	        url: site_url + "revision/switchRevision",
	        	        dataType: "json",
	        	        data: { revision_id : revision_id },
	        	        success: function (result) 
	        	        {
			            	location.href = site_url + "settings/base";
	        			},
	        	        error: function (xhr, ajaxOptions, thrownError) 
	        	        {
	        	            alert('Error occured while switching to new revision.');
	        	        },
	        	        async: false
	        	    });
	            }
	            else
	            {
	            	alert.addClass("alert-danger");
	            	
		            for(var i = 0; i < res.errors.length; i++)
		            {
		            	alert.append(res.errors[i] + "<br />");
		            }
		            
		        	$("#revisionModal #alert_panel").append(alert);
	            }

			},
	        error: function (xhr, ajaxOptions, thrownError) 
	        {
	            alert('Error occured while creating new revision.');
	        },
	        async: false
	    });
		
	});
});
</script>