<style>
table#new_revision_table tr td select {width: 100%;}
p.error { color: red; }
</style>
<div class="container-fluid">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<div class="table-responsive" style="width: 800px; margin: auto;">
				<h3>Current Revision</h3>
				<?php if(empty($open_revision_users)) : ?>
				<p>There is no current revision in progress. You may create a new revision.</p>
				<?php else: ?>
				<h4>Version : <?php echo str_replace('_', '.', $open_revision_users['revision_name']); ?></h4>
				<table id="current_revision_table" class="table" >
			  		<thead>
			  			<tr>
			  				<th width="">Language</th>
			  				<th width="">Translator</th>
			  				<th width="">Proof Reader</th>
			  				<th width="">Moderator</th>
			  			</tr>
			  		</thead>
			  		<tbody>
				  		<?php foreach ($open_revision_users['users'] as $user) : ?>
						<tr>
							<td><?php echo $user['language']; ?></td>
							<td><?php echo $user['translator']; ?></td>
							<td><?php echo $user['proofer']; ?></td>
							<td><?php echo $user['moderator']; ?></td>
						</tr>	
				  		<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			
			<div class="table-responsive" style="width: 800px; margin: auto;">
				<h3>Create a new revision</h3>
				<?php if(empty($open_revision_users)) : ?>
				<?php if (validation_errors() != '') : ?>
	            <p><?php echo validation_errors(); ?></p>
				<?php endif; ?>
				
				<?php if((isset($_POST['new_revision_lang_dir_form']) && validation_errors() != '') 
							|| (!isset($_POST['new_revision_lang_dir_form']) && !isset($_POST['new_revision_form']))) : ?>
				<form action="" method="post" >
					<span>Language Directory :</span>
					<input type="text" size="255" name="language_root_directory" value="<?php echo set_value('language_root_directory'); ?>" style="width: 400px;" />
					<div>( Relative Directory : <?php echo APPPATH; ?>)</div>
					<br />
					<br />
					<input type="submit" name="new_revision_lang_dir_form" value="Register For New Revision" />
				</form>
				<?php else:  ?>
				<?php if ($syntax_err_files = $this->session->flashdata('lang_syntax_error')) : ?>
				<p style="color: red;">Syntax error(s) found in following *_lang.php files for selected language(s).</p>
				<div>
					<?php foreach ($syntax_err_files as $file) : ?>
					<span style="padding: 10px; "><?php echo $file; ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				
				<form action="" method="post" >
				<table id="new_revision_table" class="table" >
			  		<thead>
			  			<tr>
			  				<th width="">&nbsp;</th>
			  				<th width="">Language</th>
			  				<th width="">Translator</th>
			  				<th width="">Proof Reader</th>
			  				<th width="">Moderator</th>
			  				<th width="">Directory</th>
			  			</tr>
			  		</thead>
			  		<tbody>
						<?php foreach ($languages as $language) : ?>
						<tr>
							<td><input type="checkbox" name="languages[]" <?php echo set_checkbox('languages[]', $language['name']); ?> value="<?php echo $language['name']; ?>" /></td>
							<td><?php echo $language['name']; ?></td>
							<td>
								<select name="translators[]">
									<?php foreach ($users as $user): ?>
									<option value="<?php echo $user['userid']; ?>" <?php echo set_select('translators[]', $user['userid']); ?> ><?php echo $user['username']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select name="proofers[]">
									<?php foreach ($users as $user): ?>
									<option value="<?php echo $user['userid']; ?>" <?php echo set_select('proofers[]', $user['userid']); ?> ><?php echo $user['username']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select name="moderators[]">
									<?php foreach ($users as $user): ?>
									<option value="<?php echo $user['userid']; ?>" <?php echo set_select('moderators[]', $user['userid']); ?> ><?php echo $user['username']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<select name="lang_dirs[]">
									<?php foreach ($folder_list as $folder): ?>
									<option value="<?php echo $folder; ?>" <?php echo set_select('lang_dirs[]', $folder); ?> ><?php echo $folder; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<?php endforeach; ?>
			  		</tbody>
				</table>
				<input type="hidden" size="255" name="language_root_directory" value="<?php echo set_value('language_root_directory'); ?>" />
				<p style="text-align: right;">Language Root Directory : <?php echo APPPATH.$_POST['language_root_directory']; ?></p>
				
				<div style="text-align: right; padding: 10px; ">
					<span>Revision Number :</span>
					<input type="text" size="10" name="new_revision_number" value="<?php echo set_value('new_revision_number'); ?>" style="margin-right: 20px;" />
					<span>Master Language :</span>
					<select name="master_language" style="" >
					<?php foreach ($languages as $language) : ?>
						<option value="<?php echo $language['name']; ?>" <?php echo set_select('master_language', $language['name'], $language['name'] == 'English'); ?> ><?php echo $language['name']; ?></option>
					<?php endforeach; ?>
					</select>
					<br />
					<br />
					<input type="hidden" name="master_lang_dir" />
					<input type="submit" name="new_revision_form" value="Create a New Revision" style="display: none;" />
					<button id="do_create_revision">Create a New Revision</button>
				</div>
				</form>
				<?php endif; ?>
				<?php else : ?>
				<p>Since there is a revision in progress, you can not create a new revision.</p>
				<?php endif; ?>
			</div>
			
		</div>
	</div>
</div>
<script>
function validateNewRevision()
{
	$.each($("#new_revision_table tbody tr"), function(index, tr){
		var checked = $(tr).find("input[type='checkbox']")[0].checked;
		if(!checked) $(tr).remove();
	});
	
	return true;
}

$(document).ready(function()
{
	$("#do_create_revision").click(function()
	{
		validateNewRevision();

		$("input[name='new_revision_form']").click();
	});

	var master_lang = $("select[name='master_language']").val();

	$.each($("#new_revision_table tbody tr"), function(index, tr)
	{
		if($(tr).find('td:eq(0) input').val() != master_lang) return true;

		$(tr).find("td select[name='lang_dirs[]']").change(function(){
			var master_lang_dir = $(this).val();
			$("input[name='master_lang_dir']").val(master_lang_dir);
		});
	});
});
</script>