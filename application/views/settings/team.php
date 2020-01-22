<style>
#team_configure .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
#team_configure .basic_table .dropdown ul.dropdown-menu {
	max-height: 200px;
	overflow: auto;
	min-width: 50px; 
}
#team_configure .basic_table .dropdown ul.dropdown-menu li a {
	font-size: 11px;
}
#team_configure .basic_table button {
	font-size: 11px; padding: 2px 11px;
}
#team_configure .basic_table tbody tr td select { border: medium none; }
</style>
<?php 
	$langMap = array();
	foreach($languages as $language)
	{
		$langMap[$language['id']] = $language['name']; 
	}
	
	$userMap = array();
	foreach($all_users as $user)
	{
		$userMap[$user['id']] = $user; 
	}
	
	$current_revision = $this->session->userdata('current_revision');
?>
<div style="height: 100%; " id="team_configure">
	<div class="sub-title">TEAM CONFIGURATION</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th width="160px">Language</th>
	  				<th>Translator</th>
	  				<th>Proof Reader</th>
	  				<th>Moderator</th>
	  				<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
	  				<th>Path</th>
	  				<?php endif; ?>
	  				<th width="50px" >Actions</th>
	  			</tr>
	  		</thead>
	  		<tbody>
				<?php foreach ($current_revision['users'] as $rev_user) : ?>
  				<?php if($this->session->userdata('is_global_admin') != 'yes' && $rev_user['moderator_id'] != $this->session->userdata('id')) continue; ?>
	  			<tr userid_db = "<?php echo $rev_user['id'];?>">
					<td lang_id="<?php echo $rev_user['language_id']; ?>"><?php echo $langMap[$rev_user['language_id']]; ?></td>		
					<td user_id="<?php echo $rev_user['translator_id']; ?>" ><?php echo isset($userMap[$rev_user['translator_id']]) ? $userMap[$rev_user['translator_id']]['username'] : ''; ?></td>
					<td user_id="<?php echo $rev_user['proofer_id']; ?>" ><?php echo isset($userMap[$rev_user['proofer_id']]) ? $userMap[$rev_user['proofer_id']]['username'] : ''; ?></td>
					<td user_id="<?php echo $rev_user['moderator_id']; ?>" ><?php echo $userMap[$rev_user['moderator_id']]['username']; ?></td>
					<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
					<td><?php echo $rev_user['path']; ?></td>
					<td>
						<div class="dropdown">
							<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
								 Do <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu" >
						    	<li><a tabindex="-1" href="#" data-toggle="modal" data-target="#teamLangModal" >Edit</a></li>
						    	<li><a tabindex="-1" href="#" class="team_user_delete_btn">Delete</a></li>
						  	</ul>
						</div>
					</td>
					<?php else :?>
					<td>
						<button class="btn" type="button" data-toggle="modal" data-target="#teamLangModal" >Edit</button>
					</td>
				    <?php endif; ?>
	  			</tr>
				<?php endforeach; ?>
	  		</tbody>
		</table>
	</div>
	<div style="text-align: right; ">
		<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
		<button id='team_add_configure' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;" data-toggle="modal" data-target="#teamLangModal" >ADD NEW</button>
		<?php endif; ?>
		<button id='team_save_configure' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;" >SAVE</button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#team_configure #team_save_configure").click(function()
	{
		var langs = [];
		var paths = [];
		var error_found = false;
		
		var team_entry_list = [];
		$.each($("#team_configure .basic_table tbody tr"), function(index, tr)
		{
			var entry = {
							id: $(tr).attr("userid_db"),
							language_id: $(tr).find("td:eq(0)").attr('lang_id'), 
							translator_id : $(tr).find("td:eq(1)").attr('user_id'),
							proofer_id : $(tr).find("td:eq(2)").attr('user_id'),
							moderator_id : $(tr).find("td:eq(3)").attr('user_id'),
							<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
							path : $(tr).find("td:eq(4)").html()
							<?php endif; ?>
						};

			<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
			if($.inArray(entry.language_id, langs) < 0) { langs.push(entry.language_id) }
			else
			{
				var alert = $('<div class="alert alert-danger" >Team language should not be duplicated.</div>');
				open_simple_dialog({ type: 'notification', title: 'Team Language Duplicated', message: alert[0].outerHTML });
				error_found = true; return false;
			}

			if($.inArray(entry.path, paths) < 0) { paths.push(entry.path) }
			else
			{
				var alert = $('<div class="alert alert-danger" >Team language path should not be duplicated.</div>');
				open_simple_dialog({ type: 'notification', title: 'Team Language Path Duplicated', message: alert[0].outerHTML });
				error_found = true; return false;
			}
			<?php endif; ?>
			team_entry_list.push(entry);
		});

		if(error_found) return;
		
		$.ajax({
	        type: "post",
	        cache: false,
	        url: site_url + "revision/saveTeamConfigure",
	        dataType: "json",
	        data: { rev_id : "<?php echo $current_revision['id']; ?>", team_entry_list: team_entry_list, is_global_admin: "<?php echo $this->session->userdata('is_global_admin'); ?>" },
	        success: function (res) 
	        {
	        	var alert = $('<div class="alert" ></div>');
	        	
	            if(res.errors.length == 0)
	            {
	            	alert.addClass("alert-success").append("Updated Successfully.");
	            }
	            else
	            {
	            	alert.addClass("alert-danger");
	            	
		            for(var i = 0; i < res.errors.length; i++)
		            {
		            	alert.append(res.errors[i] + "<br />");
		            }
	            }

				open_simple_dialog
				({
					type: 'notification', title: 'Team Configuration', message: alert[0].outerHTML,
					callback: function(e)
					{
						if(res.errors.length < 1) location.href = site_url + "settings/team";
					}
				}); 
	            
			},
	        error: function (xhr, ajaxOptions, thrownError) {
	            alert('Error occured while team configuration.');
	        },
	        async: false
	    });
	});

	<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
	$("#team_configure").on('click', '.team_user_delete_btn', function()
	{
		var parents = $(this).parents('tr');
		var tr = parents.first();
		var lang_name = $(tr).find("td:eq(0)").html();

		open_simple_dialog(
		{
			type: 'select', 
			title: 'Language Delete Confirmation', 
			message: "Are you sure to delete '" + lang_name + "' language from current team ? ",
			buttons: ['Yes', 'Cancel'],
			callback: function(selected)
			{
				if(selected != 'Yes') return;
				$(tr).remove();
			}
		});
	});
	<?php endif; ?>
});
</script>
