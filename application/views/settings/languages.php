<style>
#global_languages .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
#global_languages .basic_table .dropdown ul.dropdown-menu {
	max-height: 200px;
	overflow: auto;
	min-width: 50px; 
}
#global_languages .basic_table .dropdown ul.dropdown-menu li a {
	font-size: 11px;
}
#global_languages .basic_table .dropdown button.dropdown-toggle {
	font-size: 11px; padding: 2px 11px;
}
</style>

<div style="height: 100%;" id="global_languages">
	<div class="sub-title">GLOBAL LANGUAGES</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th>No.</th>
	  				<th>Language Name</th>
	  				<th>Default</th>
	  				<th>Revisions Related</th>
	  				<th width="20px" >Actions</th>
	  			</tr>
	  		</thead>
	  		<tbody>
				<?php foreach ($languages as $language) : ?>
	  			<tr langid_db="<?php echo $language['id']; ?>" >
					<td></td>		
					<td><?php echo $language['name']; ?></td>		
					<td class="default"><?php echo $language['is_default'] == 'yes' ? 'Master Language' : ''; ?></td>		
					<td>
						<?php 
							$any_revision_related = false;
							if($this->session->has_userdata('related_revisions'))
							{
								$related_revisions = $this->session->userdata('related_revisions');
								foreach ($related_revisions as $revision)
								{
									if(!isset($revision['users']) || empty($revision['users'])) continue;
									
									foreach ($revision['users'] as $entry)
									{
										if(	$entry['language_id'] != $language['id'] ) continue;
										echo "<div>".$revision['revision_name']."</div>";
										$any_revision_related = true;
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
						    	<li><a tabindex="-1" href="#" data-toggle="modal" data-target="#langModal" >Edit</a></li>
						    	<?php if($language['is_default'] == 'no' && !$any_revision_related) : ?>
						    	<li><a tabindex="-1" href="#" class="lang_delete_btn">Delete</a></li>
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
		<button data-toggle="modal" data-target="#langModal" style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;"">ADD NEW</button>
		<button id='lang_save_btn' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;" >SAVE</button>
	</div>
</div>
<script type="text/javascript">
function autoNumbering()
{
	$.each($("#global_languages .basic_table tbody tr"), function(index, tr)
	{
		$(tr).find('td:eq(0)').html(index + 1);
	});
}

$(document).ready(function()
{
	var langs = <?php echo json_encode($languages); ?>;

	autoNumbering();

	$("#global_languages").on('click', '.lang_delete_btn', function()
	{
		var parents = $(this).parents('tr');
		var tr = parents.first();
		var lang_name = $(tr).find("td:eq(1)").html();
	
		open_simple_dialog(
		{
			type: 'select', 
			title: 'Language Delete Confirmation', 
			message: "Are you sure to delete language '" + lang_name + "' ?",
			buttons: ['Yes', 'Cancel'],
			callback: function(selected)
			{
				if(selected != 'Yes') return;
				$(tr).remove();
				autoNumbering();
			}
		});
	});

	$("#global_languages #lang_save_btn").click(function()
	{
		var lang_list = [];
		$.each($("#global_languages .basic_table tbody tr"), function(index, tr)
		{
			var lang_info = { 	id : $(tr).attr('langid_db'), 
								name : $(tr).find('td:eq(1)').html(), 
								is_default : $(tr).find('td:eq(2)').html() == "" ? 'no' : 'yes' };
			lang_list.push(lang_info);
		});
		
		$.ajax({
	        type: "post",
	        cache: false,
	        url: site_url + "settings/saveGlobalLanguages",
	        dataType: "json",
	        data: { lang_list: lang_list },
	        success: function (res)
	        {
	        	var alert = $('<div class="alert"></div>');
	            if(res.errors.length == 0)
	            {
	            	alert.addClass("alert-success").append("Updated Successfully.");
	
	            	$.each($("#global_languages tbody tr"), function(index, tr)
					{
						if($(tr).attr('langid_db') == "")
			            	$(tr).attr('langid_db', res.inserted_ids.shift());
	            	});
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
					type: 'notification', title: 'Global Languages Save', message: alert[0].outerHTML,
					callback: function(e)
					{
						if(res.errors.length < 1) location.href = site_url + "settings/languages";
					}
				}); 
			},
	        error: function (xhr, ajaxOptions, thrownError) 
	        {
	            alert('Error occured while saving language information.');
	        },
	        async: false
	    });
	});
});
</script>
