<?php 
	$current_revision = $this->session->userdata('current_revision'); 
	$related_revisions = $this->session->userdata('related_revisions');
?>
<style>
#my_revisions .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
#my_revisions .basic_table button {
    background-color: #464749;
    border: medium none;
    color: #f5f5f5;
    font-size: 10px;
    padding: 8px 0;
    width: 90%;
	min-width: 50px;
}
</style>
<?php 
	$langMap = array();
	foreach($languages as $language)
	{
		$langMap[$language['id']] = $language['name']; 
	}
?>
<div style="height: 100%;" id="my_revisions">
	<div class="sub-title">MY REVISIONS
		<?php if($this->session->has_userdata('current_revision')) : ?> 
		<span style="color: #4CB781; font-size: 20px; float: right;">
			Current : <?php echo $current_revision['revision_name']; ?>
		</span>
		<?php endif; ?>
	</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th>Rev. Name</th>
	  				<th>Base Setting</th>
	  				<th>Language</th>
	  				<th>Role</th>
	  				<th>Status</th>
	  				<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
	  				<th>Delete</th>
	  				<?php endif; ?>
	  			</tr>
	  		</thead>
	  		<tbody>
				<?php if($this->session->has_userdata('related_revisions')) : ?>
				<?php foreach ($related_revisions as $revision) : ?>
				<?php if(!isset($revision['users']) || empty($revision['users'])) : ?>
	  			<tr rev_id='<?php echo $revision['id']; ?>'>
					<td><?php echo $revision['revision_name']; ?></td>		
					<td>
						Target site : <?php echo $revision['target_ci_url']; ?><br/>
						Master language : <?php echo $langMap[$revision['master_lang_id']]; ?>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
					<td class="rev_delete" style="vertical-align: middle; text-align: center;"><button>Delete</button></td>
					<?php endif; ?>
	  			</tr>
				<?php else : ?>
				<?php 
					$revision_users = array();
					if($this->session->userdata('is_global_admin') == 'yes') $revision_users = $revision['users'];
					else 
					{
						foreach ($revision['users'] as $user)
						{
							if($user['translator_id'] == $this->session->userdata('id')) { $revision_users[] = $user; continue; } 
							if($user['proofer_id'] == $this->session->userdata('id')) { $revision_users[] = $user; continue; } 
							if($user['moderator_id'] == $this->session->userdata('id')) { $revision_users[] = $user; continue; } 
						}
					}
				?>
				<?php $first = true; ?> 
				<?php foreach ($revision_users as $user) : ?>
	  			<tr rev_id='<?php echo $revision['id']; ?>'>
					<?php if($first) : ?>
					<td rowspan="<?php echo count($revision_users); ?>" ><?php echo $revision['revision_name']; ?></td>		
					<td rowspan="<?php echo count($revision_users); ?>" >
						Target site : <?php echo $revision['target_ci_url']; ?><br/>
						Master language : <?php echo $langMap[$revision['master_lang_id']]; ?>
					</td>
					<?php endif; ?>
					<td><?php echo $langMap[$user['language_id']]; ?></td>
					<td>
						<?php if($user['translator_id'] == $this->session->userdata('id')) echo '<div>Translator</div>'; ?>
						<?php if($user['proofer_id'] == $this->session->userdata('id')) echo '<div>Proof Reader</div>'; ?>
						<?php if($user['moderator_id'] == $this->session->userdata('id')) echo '<div>Moderator</div>'; ?>
					</td>
					<td><?php echo $user['lang_status']; ?></td>
					<?php if($first && $this->session->userdata('is_global_admin') == 'yes') : ?>
					<td class="rev_delete" style="vertical-align: middle; text-align: center;" rowspan="<?php echo count($revision_users); ?>"><button>Delete</button></td>
					<?php endif; ?>
	  			</tr>
				<?php $first = false; ?>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php endforeach; ?>
				<?php endif; ?>
	  		</tbody>
		</table>
		<?php if(!$this->session->has_userdata('related_revisions')) : ?>
		<div style="margin: 30px 0; text-align: center;">You are attending to no revisions.</div>
		<?php endif; ?>
	</div>
	<style>
	#my_revisions .dropup ul.dropdown-menu li {}
	#my_revisions .dropup ul.dropdown-menu li a { color: #f5f5f5; font-size: 12px; }
	#my_revisions .dropup ul.dropdown-menu li a:hover {background-image: none; font-weight: bold; background-color: transparent;}
	</style>
	<div style="text-align: right;">
		<div class="btn-group dropup" style="display: inline-flex;">
			<button class="btn dropdown-toggle" type="button" data-toggle="dropdown" style="font-family: Lato Regular; font-size: 12px; background-color: #4cb781; color: #f5f5f5; width: 200px; border-radius: unset; border: medium none; padding: 10px 0px;">
				 SWITCH REVISION TO <span class="caret" style="left: 13px; position: relative;"></span>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu" style="margin: 0px; background-color: #4cb781; width: 100%; border-radius: 0;">
				<?php if($this->session->has_userdata('related_revisions')) : ?>
				<?php foreach ($related_revisions as $revision) : ?>
				<?php if($revision['id'] == $current_revision['id']) continue; ?>
		    	<li rev_id="<?php echo $revision['id']; ?>" >
		    		<a tabindex="-1" href="#"><?php echo $revision['revision_name']; ?></a>
		    	</li>
				<?php endforeach; ?>
				<?php endif; ?>
		  	</ul>
		</div>
		<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
		<button id='rev_add_configure' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 200px; font-family: Lato Regular; font-size: 12px;" data-toggle="modal" data-target="#revisionModal" >CREATE NEW REVISION</button>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#my_revisions .dropup ul.dropdown-menu li").click(function()
	{
		var revision_id = $(this).attr('rev_id');

		$.ajax
		({
	        type: "post",
	        cache: false,
	        url: site_url + "revision/switchRevision",
	        dataType: "json",
	        data: { revision_id : revision_id },
	        success: function (result) 
	        {
            	location.href = site_url + 'settings/base';
			},
	        error: function (xhr, ajaxOptions, thrownError) 
	        {
	            alert('Error occured while switching to new revision.');
	        },
	        async: false
	    });
	});

	$("#my_revisions .basic_table tbody tr td.rev_delete button").click(function()
	{
		var revision_id = $(this).parent().parent().attr('rev_id');
		var revision_name = $(this).parent().parent().find('td:eq(0)').html();

		open_simple_dialog
		({ 	
			type: 'select', 
			title: ' Remove a revision', 
			message: "Are you sure to remove revision '" + revision_name + "' ?",
			buttons: ['Yes', 'Cancel'],
			callback: function(selected)
			{
				if(selected != 'Yes') return;

				$.ajax
				({
			        type: "post",
			        cache: false,
			        url: site_url + "revision/removeRevision",
			        dataType: "json",
			        data: { revision_id : revision_id },
			        success: function (result) 
			        {
		            	location.href = site_url + 'settings/revisions';
					},
			        error: function (xhr, ajaxOptions, thrownError) 
			        {
			            alert('Error occured while removing selected revision.');
			        },
			        async: false
			    });
			}
		});
	});
});
</script>
