<!-- Modal -->
<style>
	#teamLangModal .input-group { padding: 2px; }
	#teamLangModal .input-group .form-control { min-height: 34px; }
	#teamLangModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
	#teamLangModal input.form-control { font-size: 12px;}
	#teamLangModal ul.dropdown-menu { max-height: 200px; min-width: 50px; overflow: auto; }
	#teamLangModal ul.dropdown-menu li a { font-size: 12px; }

#teamLangModal #languages_supported > span {
  background-color: #ece9d8;
  border-radius: 4px;
  display: inline-block;
  float: left;
  font-size: 11px;
  margin: 0 0 2px 2px;
  padding: 2px 6px 2px 10px;
}
	
#teamLangModal #languages_supported > span span {
  background-color: #dddbd0;
  border-radius: 6px;
  cursor: pointer;
  margin-left: 4px;
  padding: 0 4px 0 5px;
}
#teamLangModal .modal-body .form-control { height: auto; }
#teamLangModal #alert_panel .alert {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 22px;
  padding: 5px 17px;
}
#teamLangModal .form-control[disabled], 
#teamLangModal .form-control[readonly], 
#teamLangModal fieldset[disabled] .form-control 
{
  background-color: transparent;
  cursor: not-allowed;
  opacity: 1;
}
</style>
<div class="modal fade" id="teamLangModal" tabindex="-1" role="dialog" aria-labelledby="teamLangModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 660px; margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="teamLangModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
			        <span class="input-group-addon">Language</span>
					<select name="language" class="form-control" <?php echo $this->session->userdata('is_global_admin') == 'yes' ? '' : 'disabled'; ?> >
						<?php foreach ($languages as $language) : ?>
						<option value="<?php echo $language['id']; ?>" default="<?php echo $language['is_default']; ?>"><?php echo $language['name']?></option>
						<?php endforeach; ?>
					</select>
		        </div>
				<div class="input-group">
			        <span class="input-group-addon">Translator</span>
					<select name="translator" class="form-control"></select>
		        </div>
				<div class="input-group">
			        <span class="input-group-addon">Proof Reader</span>
					<select name="proofer" class="form-control"></select>
		        </div>
				<div class="input-group moderator">
			        <span class="input-group-addon">Moderator</span>
					<select name="moderator" class="form-control" <?php echo $this->session->userdata('is_global_admin') == 'yes' ? '' : 'disabled'; ?> ></select>
		        </div>
		        <?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
				<div class="input-group" >
			        <span class="input-group-addon">Path</span>
			        <div class="input-group-addon" style="max-width: inherit; padding-right: 0px; border-right: medium none; background-color: transparent;"><?php $cr = $this->session->userdata('current_revision'); echo ROOTPATH.$cr['lang_root_dir'].'/'; ?></div>
					<input type="text" name="path" class="form-control" style="padding-left: 0px; border-left: medium none;" />
		        </div>
		        <?php endif; ?>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="team_action_exe">OK</button>
			</div>
		</div>
	</div>
</div>
<script><!--
$(document).ready(function()
{
	var language = $("#teamLangModal select[name='language']");
	var translator = $("#teamLangModal select[name='translator']");
	var proofer = $("#teamLangModal select[name='proofer']");
	var moderator = $("#teamLangModal select[name='moderator']");
	<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
	var path = $("#teamLangModal input[name='path']");
	<?php endif; ?>
	
	var all_users = [];
	<?php foreach ($users as $user) : ?>
	all_users.push({id: "<?php echo $user['id']; ?>", name: "<?php echo $user['username']; ?>", 
					lang_ids : <?php echo $user['language_ids']; ?>, global_admin : "<?php echo $user['is_global_admin']; ?>"});
	<?php endforeach; ?>

	var tr_in_edit;
	
	$('#teamLangModal').on('show.bs.modal', function (ev) 
	{
		$(this).find("#alert_panel").empty();
		
		var btn = ev.relatedTarget;

		if($(btn).html() == 'ADD NEW')
		{
			$(this).find(".modal-title").html('Create A New Team Configuration Entry');
			language.val($(this).find("select[name='language'] option:first").val()).change();
			translator.val($(this).find("select[name='translator'] option:first").val());
			proofer.val($(this).find("select[name='proofer'] option:first").val());
			moderator.val($(this).find("select[name='moderator'] option:last").val()).change();
			<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
			path.val('');
			<?php endif; ?>
		}
		else
		{
			$(this).find(".modal-title").html('Edit Team Configuration Entry');
			var parents = $(btn).parents('tr');
			tr_in_edit = parents.first();
			language.val($(tr_in_edit).find('td:eq(0)').attr('lang_id')).change();
			translator.val($(tr_in_edit).find('td:eq(1)').attr('user_id'));
			proofer.val($(tr_in_edit).find('td:eq(2)').attr('user_id'));
			moderator.val($(tr_in_edit).find('td:eq(3)').attr('user_id')).change();
			<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
			path.val($(tr_in_edit).find('td:eq(4)').html());
			<?php endif; ?>
		}
	});

	$("#teamLangModal #team_action_exe").click(function()
	{
		<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
		if(path.val().trim() == "")
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
            alert.append("Empty path value is not allowed for language.");

            $("#teamLangModal #alert_panel").empty().append(alert);
            return;
		}
		<?php endif; ?>
		
		<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
		if(moderator.find('option:selected').attr('global_admin') != 'yes' && (translator.val() != "0" || proofer.val() != "0"))
		{
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
            alert.append("Once a moderator is assigned for a language, translator and proofer is to be assigned by the moderator.");

            $("#teamLangModal #alert_panel").empty().append(alert);
            return;
		}
		
		if(moderator.find('option:selected').attr('global_admin') == 'yes' && translator.val() == "0")
		{
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
            alert.append("Once you are a moderator, translator must be assigned by you.");

            $("#teamLangModal #alert_panel").empty().append(alert);
            return;
		}
		<?php else : ?>
		if(translator.val() == "0")
		{
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
            alert.append("Once you are a moderator, translator must be assigned by you.");

            $("#teamLangModal #alert_panel").empty().append(alert);
            return;
		}
		<?php endif; ?>
		$("#teamLangModal").modal('hide');

		if($("#teamLangModal .modal-title").html() == "Edit Team Configuration Entry")
		{
			$(tr_in_edit).find('td:eq(0)').attr('lang_id', language.val()).html(language.find("option:selected").html());
			$(tr_in_edit).find('td:eq(1)').attr('user_id', translator.val()).html(translator.find("option:selected").html());
			$(tr_in_edit).find('td:eq(2)').attr('user_id', proofer.val()).html(proofer.find("option:selected").html());
			<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
			$(tr_in_edit).find('td:eq(3)').attr('user_id', moderator.val()).html(moderator.find("option:selected").html());
			$(tr_in_edit).find('td:eq(4)').html(path.val());
			<?php endif; ?>
		}
		else
		{
			var tr = $('<tr userid_db="">' + 
						'<td lang_id="' + language.val() + '">' + language.find("option:selected").html() + '</td>' +		
						'<td user_id="' + translator.val()+ '">' + translator.find("option:selected").html() + '</td>' + 
						'<td user_id="' + proofer.val()+ '">' + proofer.find("option:selected").html() + '</td>' + 
						'<td user_id="' + moderator.val()+ '">' + moderator.find("option:selected").html() + '</td>' +
						<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
						'<td>' + path.val() + '</td>' +
						'<td>' +
							'<div class="dropdown">' +
								'<button data-toggle="dropdown" type="button" class="btn dropdown-toggle">Do <span class="caret"></span></button>' +
								'<ul role="menu" class="dropdown-menu dropdown-menu-right">' +
							    	'<li><a data-target="#teamLangModal" data-toggle="modal" href="#" tabindex="-1">Edit</a></li>' +
							    	'<li><a class="team_user_delete_btn" href="#" tabindex="-1">Delete</a></li>' +
							  	'</ul>' +
							'</div>' +
						'</td>' +
						<?php else: ?>
						'<td>' +
							'<button class="btn" type="button" data-toggle="modal" data-target="#teamLangModal" >Edit</button>' + 
						'</td>' +
						<?php endif; ?>
		  			'</tr>');

			$("#team_configure .basic_table tbody").append(tr);
		}
	});

	language.change(function()
	{
		<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
		if($(this).find("option:selected").attr('default') == 'yes')
		{
			<?php $current_revision = $this->session->userdata('current_revision'); ?>
			path.attr('disabled', 'disabled').val('<?php echo $current_revision['master_lang_root_dir']; ?>');
		}
		else
		{
			var pathExpected = $(this).find("option:selected").html().toLowerCase();
			path.removeAttr('disabled').val(pathExpected);
		}
		<?php endif; ?>

		var selected_lang_id = $(this).val();
		var opt = '<option value="0"></option>';
		translator.empty().append(opt);
		proofer.empty().append(opt);
		moderator.empty();

		for(var ii = 0; ii < all_users.length; ii++)
		{
			if( $.inArray(selected_lang_id, all_users[ii].lang_ids) < 0 && all_users[ii].global_admin == 'no') continue;

			var option = '<option value="' + all_users[ii].id + '" global_admin="' + all_users[ii].global_admin + '">' + all_users[ii].name + '</option>';
			translator.append(option);
			proofer.append(option);
			moderator.append(option);
		}

		opt = '<option value="<?php echo $this->session->userdata('id'); ?>" global_admin="<?php echo $this->session->userdata('is_global_admin'); ?>"><?php echo $this->session->userdata('username'); ?></option>';
		translator.append(opt);
		proofer.append(opt);
		moderator.append(opt).change();
	});

	<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
	moderator.change(function()
	{
		if($(this).find('option:selected').attr('global_admin') == 'yes')
		{
			translator.removeAttr('disabled');
			proofer.removeAttr('disabled');
		}
		else
		{
			translator.val('0').attr('disabled', 'disabled');
			proofer.val('0').attr('disabled', 'disabled');
		}
	});
	<?php endif; ?>
});
--></script>