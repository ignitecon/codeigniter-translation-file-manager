<!-- Modal -->
<style>
	#pwdModal .input-group { padding: 2px; }
	#pwdModal .input-group .form-control { height: auto; min-height: 34px; }
	#pwdModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
	#pwdModal input.form-control { font-size: 12px;}

#pwdModal #alert_panel .alert {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 22px;
  padding: 5px 17px;
}
</style>
<div class="modal fade" id="pwdModal" tabindex="-1" role="dialog" aria-labelledby="pwdModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="pwdModalLabel">Forgot Password</h4>
			</div>
			<div class="modal-body">
				<div class="input-group ">
			        <span class="input-group-addon">Your Email</span>
					<input type="text" name="email" class="form-control" />
		        </div>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="pwd_action_exe">Reset</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	$("#pwdModal #pwd_action_exe").click(function()
	{
		$("#pwdModal #alert_panel").empty();
		
		if($("#pwdModal input[name='email']").val().trim() == "")
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
        	$("#pwdModal #alert_panel").append(alert);
            alert.append("Empty email is not allowed.");
			return;
		}

		$("#pwdModal").modal('hide');
		
		var email = $("#pwdModal input[name='email']").val();
		
		$.ajax
		({
	        type: "post",
	        cache: false,
	        url: site_url + "user/sendForgetPasswordMail",
	        dataType: "json",
	        data: { user_email : email },
	        success: function (res) 
	        {
	        	var alert = $('<div class="alert" ></div>');
	            for(var i = 0; i < res.errors.length; i++) alert.append(res.errors[i] + "<br />");
	        	
	            if(res.errors.length == 0)
	            	alert.addClass("alert-success").append("A mail for reset-password is sent. Please go to your mail box.");
	            else
	            	alert.addClass("alert-danger");

					open_simple_dialog({ type: 'notification', title: 'Password Reset', message: alert[0].outerHTML	}); 
			},
	        error: function (xhr, ajaxOptions, thrownError) 
	        {
	        	open_simple_dialog({ type: 'notification', title: 'Password Reset', message: 'Error occured while password reset.' });
	        },
	        async: false
	    });
	});
});
</script>