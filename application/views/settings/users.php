<style>
#users_by_referer .sub-title { font-family: Bebas Neue; font-size: 24px; font-weight: bold; margin-bottom: 15px; }
#users_by_referer .basic_table .dropdown ul.dropdown-menu { max-height: 200px; overflow: auto; min-width: 50px; }
#users_by_referer .basic_table .dropdown ul.dropdown-menu li a { font-size: 11px; }
#users_by_referer .basic_table .dropdown button.dropdown-toggle { font-size: 11px; padding: 2px 11px; }
#users_by_referer .basic_table tbody tr td.languages { }
#users_by_referer .basic_table tbody tr td.languages > span { background-color: #ECE9D8; border-radius: 4px 4px 4px 4px; display: inline-block; margin: 0 0px 2px 2px; padding: 2px 6px 2px 10px; float: left; }
#users_by_referer .basic_table tbody tr td.lang_add  { padding-left: 0px; }
#users_by_referer .basic_table tbody tr td.password  { }
#users_by_referer .basic_table tbody tr td.password span { display: none; }
</style>

<div style="height: 100%;" id="users_by_referer">
	<div class="sub-title">USERS LIST</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th style="white-space: nowrap;">User Name</th>
	  				<th>Email</th>
	  				<th>Password</th>
	  				<th width="100%">Languages Supported</th>
	  				<th width="100%">Revisions Attending</th>
	  				<th width="20px" >Actions</th>
	  			</tr>
	  		</thead>
	  		<tbody>
		  		<?php 
		  			$lang_map = array(); 
		  			foreach ($languages as $language)
		  			{
		  				$lang_map[$language['id']] = $language['name']; 
		  			}
		  		?>
				<?php foreach ($all_users as $user) : ?>
				<?php if(!in_array($this->session->userdata('id'), array($user['id'], $user['refer_id']))) continue; ?>
	  			<tr userid_db="<?php echo $user['id']; ?>" >
					<td><?php echo $user['username']; ?></td>		
					<td><?php echo $user['email']; ?></td>		
					<td class="password">************<span>************</span></td>		
					<td class="languages">
						<?php $user_lang_ids = json_decode($user['language_ids'], TRUE); ?>
						<?php foreach ($user_lang_ids as $user_lang_id) : ?>
						<span lang_id="<?php echo $user_lang_id; ?>" ><?php echo $lang_map[$user_lang_id]; ?></span>
						<?php endforeach; ?>
					</td>
					<td>
						<?php 
							if($this->session->has_userdata('related_revisions'))
							{
								$related_revisions = $this->session->userdata('related_revisions');
								foreach ($related_revisions as $revision)
								{
									if(!isset($revision['users']) || empty($revision['users'])) continue;
									
									foreach ($revision['users'] as $entry)
									{
										if(	$entry['translator_id'] != $user['id'] && $entry['proofer_id'] != $user['id'] && $entry['moderator_id'] != $user['id'] ) continue;
										echo "<div>".$revision['revision_name']."</div>";
										break; 
									}
								}
							}
						?>
					</td>
					<td>
						<div class="dropdown">
							<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
								 Do <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu" >
						    	<li><a tabindex="-1" href="#" data-toggle="modal" data-target="#userModal" >Edit</a></li>
						    	<?php if($user['id'] != $this->session->userdata('id')) :?>
						    	<li><a tabindex="-1" href="#" class="user_delete_btn">Delete</a></li>
						    	<?php endif; ?>
						  	</ul>
						</div>
					</td>
	  			</tr>
				<?php endforeach; ?>
	  		</tbody>
		</table>
	</div>
	<div style="text-align: right; ">
		<?php 
			$can_create_user = false;
			if($this->session->userdata('is_global_admin') == 'yes') $can_create_user = true;
			elseif($this->session->has_userdata('related_revisions')) 
			{
				$related_revisions = $this->session->userdata('related_revisions');
				foreach ($related_revisions as $revision)
				{
					if(!isset($revision['users']) || empty($revision['users'])) continue;
					
					foreach ($revision['users'] as $entry)
					{
						if($entry['moderator_id'] != $this->session->userdata('id') ) continue;
						$can_create_user = true;
						break; 
					}
					
					if($can_create_user) break;
				}
			}
		?>
  		<?php if($can_create_user) : ?>
		<button id="usr_new_user" data-toggle="modal" data-target="#userModal" style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;"">ADD NEW</button>
		<?php endif; ?>
		<button id='usr_save_user_list' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;" >SAVE</button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#users_by_referer .basic_table .language-dropdown ul.dropdown-menu li").click(function()
	{
		var selected_lang_id = $(this).attr('lang_id');
		var selected_lang = $(this).find('a').html();
		var parents = $(this).parents('tr');
		var tr = parents.first();
		var included = false;
		$.each(tr.find('td.languages span'), function(index, span)
		{
			if($(span).attr('lang_id') == selected_lang_id) { included = true; return false; }
		});

		if(!included)
		{
			var span = $("<span lang_id='" + selected_lang_id + "'>" + selected_lang + "</span>");
			tr.find('td.languages').append(span);
		}
	});

	$("#users_by_referer #usr_save_user_list").click(function()
	{
		var referer_id = "<?php echo $this->session->userdata('id'); ?>";
		var user_list = [];
		$.each($("#users_by_referer .basic_table tbody tr"), function(index, tr)
		{
			var language_ids = [];
			$.each($(tr).find('td:eq(3) > span'), function(ind, span){ language_ids.push($(span).attr('lang_id')); });
			var user_info = { id : $(tr).attr('userid_db'), refer_id : referer_id, username : $(tr).find('td:eq(0)').html(), 
							  email : $(tr).find('td:eq(1)').html(), password : $(tr).find('td:eq(2) span').html(), language_ids : language_ids };
			user_list.push(user_info);
		});
		
		$.ajax
		({
	        type: "post",
	        cache: false,
	        url: site_url + "user/saveUsersWithReferer",
	        dataType: "json",
	        data: { referer_id : referer_id, user_list: user_list },
	        success: function (res) 
	        {
	        	var alert = $('<div class="alert"></div>');
	            if(res.errors.length == 0)
	            {
	            	alert.addClass("alert-success").append("Updated Successfully.");

//	            	$.each($("#users_by_referer tbody tr"), function(index, tr)
//            		{
//	            		if($(tr).attr('userid_db') == "")
//		            		$(tr).attr('userid_db', res.inserted_ids.shift());
//            		});
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
					type: 'notification', title: 'Users List', message: alert[0].outerHTML,
					callback: function(e)
					{
						if(res.errors.length < 1) location.href = site_url + "settings/users";
					}
				}); 
			},
	        error: function (xhr, ajaxOptions, thrownError) {
	            alert('Error occured while users information.');
	        },
	        async: false
	    });
	});

	$("#users_by_referer").on('click', '.user_delete_btn', function()
	{
		var parents = $(this).parents('tr');
		var tr = parents.first();
		var user_name = $(tr).find("td:eq(0)").html();
		var email = $(tr).find("td:eq(1)").html();
		var user_id = $(tr).attr('userid_db');

		open_simple_dialog(
		{
			type: 'select', 
			title: 'User Delete Confirmation', 
			message: "Are you sure to delete user with <br />name : '" + user_name + "', email : '" + email + "' ?",
			buttons: ['Yes', 'Cancel'],
			callback: function(selected)
			{
				if(selected != 'Yes') return;
				$(tr).remove();
			}
		});
	});
});
</script>
