<!-- Modal -->
<style>
#userModal .input-group { padding: 2px; }
#userModal .input-group .form-control { height: auto; min-height: 34px; }
#userModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
#userModal input.form-control { font-size: 12px;}
#userModal ul.dropdown-menu { max-height: 200px; min-width: 50px; overflow: auto; }
#userModal ul.dropdown-menu li a { font-size: 12px; }
#userModal #languages_supported > span { background-color: #ece9d8; border-radius: 4px; display: inline-block; float: left; font-size: 11px; margin: 0 0 2px 2px; padding: 2px 6px 2px 10px; }
#userModal #languages_supported > span span { background-color: #dddbd0; border-radius: 6px; cursor: pointer; margin-left: 4px; padding: 0 4px 0 5px; }
#userModal #alert_panel .alert { font-family: Lato Regular; font-size: 12px; margin: 0 22px; padding: 5px 17px; }
</style>
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="userModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="input-group ">
			        <span class="input-group-addon">User Name</span>
					<input type="text" name="username" class="form-control" />
		        </div>
				<div class="input-group ">
			        <span class="input-group-addon">Email</span>
					<input type="text" name="email" class="form-control" />
		        </div>
				<div class="input-group ">
			        <span class="input-group-addon">Password</span>
					<input type="text" name="password" class="form-control" />
			        <span class="input-group-addon">Re-type</span>
					<input type="text" name="pwd_confirm" class="form-control" />
			        <span class="input-group-addon"  style="padding: 0;">
						<button id="generate_pwd" class="btn" type="button" style="width: 100%; padding: 5px 0; font-size: 12px;">Generate</button>
			        </span>
		        </div>
				<div class="input-group">
			        <span class="input-group-addon">Languages</span>
			        <div class="form-control" id="languages_supported"></div>
			        <span class="input-group-addon"  style="padding: 0;">
						<div class="dropdown language-dropdown" style="">
							<button class="btn dropdown-toggle" type="button" data-toggle="dropdown" style="width: 100%; padding: 6px 0; font-size: 12px;">
								Add <span class="caret" style="margin-left: 10px;"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu" >
								<?php foreach ($languages as $language) : ?>
						    	<li lang_id="<?php echo $language['id']; ?>"><a tabindex="-1" href="#"><?php echo $language['name']; ?></a></li>
								<?php endforeach; ?>
						  	</ul>
						</div>
			        </span>
		        </div>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="user_action_exe">OK</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	var tr_in_edit;
	
	$('#userModal').on('show.bs.modal', function (ev) 
	{
		$(this).find("#alert_panel").empty();
		
		var btn = ev.relatedTarget;
		$(this).find("#languages_supported").empty();
		$(this).find("input[name='pwd_confirm']").val('');
		
		if($(btn).html() == 'ADD NEW')
		{
			$(this).find(".modal-title").html('Create A New User');
			$(this).find("input[name='username']").val('');
			$(this).find("input[name='email']").val('');
			$(this).find("input[name='password']").val('');
		}
		else
		{
			$(this).find(".modal-title").html('Edit User');
			var parents = $(btn).parents('tr');
			tr_in_edit = parents.first();
			$(this).find("input[name='username']").val($(tr_in_edit).find('td:eq(0)').html());
			$(this).find("input[name='email']").val($(tr_in_edit).find('td:eq(1)').html());
			$(this).find("input[name='password']").val('');
			
			$.each($(tr_in_edit).find('td:eq(3) span'), function(index, _span_)
			{
				var span = $("<span lang_id='" + $(_span_).attr('lang_id') + "'>" + $(_span_).html() + "<span>&times;</span></span>");
				$("#userModal #languages_supported").append(span);
				span.find('span').click(function(){$(this).parent().remove();});
			});
		}
	});

	$("#userModal .language-dropdown ul.dropdown-menu li").click(function()
	{
		var selected_lang_id = $(this).attr('lang_id');
		var selected_lang = $(this).find('a').html();
		var included = false;
		$.each($("#languages_supported").find('span'), function(index, span)
		{
			if($(span).attr('lang_id') == selected_lang_id) { included = true; return false; }
		});

		if(!included)
		{
			var span = $("<span lang_id='" + selected_lang_id + "'>" + selected_lang + "<span>&times;</span></span>");
			$("#languages_supported").append(span);
			span.find('span').click(function(){$(this).parent().remove();});
		}
	});
	
	$("#userModal #user_action_exe").click(function()
	{
		$("#userModal #alert_panel").empty();
		
		if($("#userModal input[name='password']").val() != $("#userModal input[name='pwd_confirm']").val())
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
        	$("#userModal #alert_panel").append(alert);
            alert.append("Password mismatch. Try again.");
            return;
		}

		if($("#userModal input[name='email']").val().trim() == "")
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
        	$("#userModal #alert_panel").append(alert);
            alert.append("Empty email address is not allowed.");
            return;
		}

		// TO DO : Mail address checking
	
		
		$("#userModal").modal('hide');
		var tr;
		
		if($("#userModal .modal-title").html() == 'Edit User')
		{
			tr = $(tr_in_edit);
		}
		else
		{
			tr = $('<tr userid_db="">' + 
						'<td></td>' + 		
						'<td></td>' + 	
						'<td class="password">************<span>************<span></td>' + 	
						'<td class="languages"></td>' + 
						'<td></td>' + 	
						'<td>' + 
							'<div class="dropdown">' + 
								'<button data-toggle="dropdown" type="button" class="btn dropdown-toggle">' + 
									 'Do <span class="caret"></span>' + 
								'</button>' + 
								'<ul role="menu" class="dropdown-menu dropdown-menu-right">' + 
							    	'<li><a data-target="#userModal" data-toggle="modal" href="#" tabindex="-1">Edit</a></li>' + 
							    	'<li><a href="#" tabindex="-1" class="user_delete_btn">Delete</a></li>' + 
							  	'</ul>' + 
							'</div>' + 
						'</td>' + 
					'</tr>');

			$("#users_by_referer .basic_table tbody").append(tr);
		}

		$(tr).find('td:eq(0)').html($("#userModal input[name='username']").val());
		$(tr).find('td:eq(1)').html($("#userModal input[name='email']").val());
		$(tr).find('td:eq(2) span').html($("#userModal input[name='password']").val());
		$(tr).find('td:eq(3)').empty();

		$.each($("#languages_supported > span"), function(index, span)
		{
			$(span).find('span').remove();
			$(tr).find('td:eq(3)').append($("<span lang_id='" + $(span).attr('lang_id') + "'>" + $(span).html() + "</span>"));
		});
	});

	$("#userModal #generate_pwd").click(function()
	{
		var pwd = "";
	    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_-+={}[]:;?<>,.";

	    for( var i=0; i < 15; i++ )
	    	pwd += possible.charAt(Math.floor(Math.random() * possible.length));

		$("#userModal input[name='password']").val(pwd);
	});
});
</script>