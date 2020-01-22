<?php $current_revision = $this->session->userdata('current_revision'); ?>

<style>
#edit_revision .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
</style>
<div id="edit_revision">
	<div class="sub-title">BASE SETTINGS</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table">
			<thead>
				<tr>
					<th width="20%">Item Name</th>
					<th width="200px">Item Value</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Revision Name</td>
					<td><input type="text" id="revision_name" value="<?php echo $current_revision['revision_name']; ?>" /></td>
					<td>The name of revision.<br />This name shouldnot be duplicated with other revision.</td>
				</tr>
				<tr>
					<td>Master Language</td>
					<td>
						<select id="master_lang_id">
							<?php foreach ($languages as $language) : ?>
							<option value="<?php echo $language['id']; ?>" <?php if($current_revision['master_lang_id'] == $language['id']) echo 'selected'; ?>><?php echo $language['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>Master Language is the standard language.<br />All other language files will be edited according to this language resource file.</td>
				</tr>
				<tr>
					<td>Language File Root Path</td>
					<td><input type="text" id="lang_root_path" value="<?php echo $current_revision['lang_root_dir']; ?>" /></td>
					<td>Root path of language resource files that is relative to<br />'<?php echo ROOTPATH; ?>'<br />This path shouldnot be duplicated with other revision.</td>
				</tr>
				<tr>
					<td>Master Language File Root Path</td>
					<td><input type="text" id="master_lang_root_path" value="<?php echo $current_revision['master_lang_root_dir']; ?>" /></td>
					<td>Root path of master language resource files that is relative to<br />'<?php echo ROOTPATH; ?><span id="lang_root_path_copy"></span>'</td>
				</tr>
				<tr>
					<td>Target Codeigniter Site URL</td>
					<td><textarea id="target_ci_url" placeholder="http(s)://example.com/index.php/" ><?php echo $current_revision['target_ci_url']; ?></textarea></td>
					<td>This is the site url to update the language resources.</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="text-align: right; ">
		<?php if($current_revision['status'] == 'Created') : ?>
		<button id='rev_save_basic_setting' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 50px; font-family: Lato Regular; font-size: 12px;" >SAVE</button>
		<?php else : ?>
		<button style="background-color: #f5f5f5; color: #4cb781; border: 1px solid black; width: 140px; padding: 10px 5px; font-family: Lato Regular; font-size: 12px;" >SETTING COMPLETED</button>
		<?php endif; ?>
	</div>
</div>
<script>
var current_rev_id = <?php echo $current_revision['id']; ?>;
$(document).ready(function()
{
	$("#edit_revision input#lang_root_path").change(function()
	{
		$("#edit_revision span#lang_root_path_copy").html($(this).val() + "/");
	});

	$("#edit_revision button#rev_save_basic_setting").click(function()
	{
		$.ajax({
	        type: "post",
	        cache: false,
	        url: site_url + "revision/saveBasicSettings",
	        dataType: "json",
	        data: { rev_id : current_rev_id, 
		        	rev_name: $("#edit_revision input#revision_name").val(), 
		        	master_lang_id : $("#edit_revision select#master_lang_id").val(), 
		        	lang_root : $("#edit_revision input#lang_root_path").val(), 
		        	master_lang_root : $("#edit_revision input#master_lang_root_path").val(),
		        	target_ci_url : $("#edit_revision textarea#target_ci_url").val() },
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
		            type: 'notification', title: 'Base Setting', message: alert[0].outerHTML,
					callback: function(e)
					{
						if(res.errors.length < 1) location.href = site_url + "settings/base";
					}
		        });
			},
	        error: function (xhr, ajaxOptions, thrownError) {
	            alert('Error occured while saving basic settings.');
	        },
	        async: false
	    });
	});

	$("#edit_revision input#lang_root_path").change();
});
</script>