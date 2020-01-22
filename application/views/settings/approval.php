<style>
#revision_operation .sub-title { font-family: Bebas Neue; font-size: 24px; font-weight: bold; margin-bottom: 15px; }
#revision_operation .basic_table .dropdown ul.dropdown-menu { max-height: 200px; overflow: auto; min-width: 50px; }
#revision_operation .basic_table .dropdown ul.dropdown-menu li a { font-size: 11px; }
#revision_operation .basic_table .dropdown button.dropdown-toggle { font-size: 11px; padding: 2px 11px; }
#revision_operation .basic_table button.operation { background-color: #464749; color: #f5f5f5; border: none; width: 100px; padding: 8px 0; width: 90%; font-size: 10px; }
#revision_operation .basic_table tr th { text-align: center; }
#revision_operation .basic_table tr td { padding: 2px 10px; text-align: center; vertical-align: middle; }
#revision_operation .basic_table tr td span { border-radius: 3px; display: inline-block; margin: 0 0 1px; padding: 1px 0; text-align: center; width: 87%; }
#revision_operation .basic_table tr td span.completed { background-color: #2da367; color: #f5f5f5; }
#revision_operation .basic_table tr td span.inprogress { background-color: blue; color: #f5f5f5; }
#revision_operation .basic_table tr td span.unchanged { background-color: red; color: #f5f5f5; }
</style>
<?php 
	$current_revision = $this->session->userdata('current_revision');
	
	$langMap = array();
	foreach($languages as $language)
	{
		$langMap[$language['id']] = $language['name'];
	}
	
	foreach ($statistics as &$statistic)
	{
		$status = array(); 
		$status['translator'] 	= array('In Progress' => 0, 'Completed' => 0);
		$status['proofer'] 		= array('In Progress' => 0, 'Completed' => 0);

		foreach ($statistic['status'] as $entry)
		{
			switch ($entry['translator_status'])
			{
				case 'In Progress'	: $status['translator']['In Progress']++; 	break;
				case 'Completed' 	: $status['translator']['Completed']++; 	break;
			}

			switch ($entry['proofer_status'])
			{
				case 'In Progress'	: $status['proofer']['In Progress']++; 	break;
				case 'Completed' 	: $status['proofer']['Completed']++; 	break;
			}
		}
			
		$status['translator']['Unchanged'] 	= count($master_files) - $status['translator']['In Progress'] 	- $status['translator']['Completed'];
		$status['proofer']['Unchanged'] 	= count($master_files) - $status['proofer']['In Progress'] 		- $status['proofer']['Completed'];

		$statistic['simple_status'] = $status;
	}
	
?>
<div style="height: 100%;" id="revision_operation">
	<div class="sub-title">APPROVAL &amp; PUBLISH</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th width="">Language</th>
	  				<th width="18%">Translation</th>
	  				<th width="18%">Proof Read</th>
	  				<th width="15%">Approval</th>
	  				<th width="15%">Zip &amp; Archive</th>
	  				<th width="15%">Publish</th>
	  			</tr>
	  		</thead>
	  		<tbody>
				<?php foreach ($statistics as $statis) : ?>
	  			<tr rev_user_id="<?php echo $statis['id']; ?>">
					<td style="font-size: 14px;"><?php echo $langMap[$statis['language_id']]; ?></td>		
					<td>
						<span class="completed"><?php echo $statis['simple_status']['translator']['Completed']; ?> file(s) Completed</span>
						<span class="inprogress"><?php echo $statis['simple_status']['translator']['In Progress']; ?> file(s) In Progress</span>
						<span class="unchanged"><?php echo $statis['simple_status']['translator']['Unchanged']; ?> file(s) Unchanged</span>
					</td>		
					<td>
						<span class="completed"><?php echo $statis['simple_status']['proofer']['Completed']; ?> file(s) Completed</span>
						<span class="inprogress"><?php echo $statis['simple_status']['proofer']['In Progress']; ?> file(s) In Progress</span>
						<span class="unchanged"><?php echo $statis['simple_status']['proofer']['Unchanged']; ?> file(s) Unchanged</span>
					</td>	
					<td>
						<?php if($statis['lang_status'] == 'In Progress') : ?>
						<button class="operation" operation="approve" >APPROVE</button>
						<?php else: ?>
						<img src="<?php echo base_url('assets/img/status-ok.png'); ?>" />
						<?php endif; ?>
					</td>		
					<td>
						<?php if($statis['lang_status'] == 'In Progress') : ?>
						<?php elseif($statis['lang_status'] == 'Approved'): ?>
						<button class="operation" operation="archive">ARCHIVE</button>
						<?php else: ?>
						<img src="<?php echo base_url('assets/img/status-ok.png'); ?>" />
						<?php endif; ?>
					</td>
					<td>
						<?php if($statis['lang_status'] == 'In Progress') : ?>
						<?php elseif($statis['lang_status'] == 'Approved'): ?>
						<?php elseif($statis['lang_status'] == 'Archived'): ?>
						<button class="operation" operation="publish">PUBLISH</button>
						<?php else: ?>
						<img src="<?php echo base_url('assets/img/status-ok.png'); ?>" />
						<?php endif; ?>
					</td>
	  			</tr>
				<?php endforeach; ?>
	  		</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#revision_operation button.operation").click(function()
	{
		var parents = $(this).parents('tr');
		var tr = parents.first();
		
		var operation = $(this).attr('operation');
		var lang_name = $(tr).find('td:eq(0)').html();
		var rev_user_id = $(tr).attr('rev_user_id');

		open_simple_dialog
		({ 	
			type: 'select', 
			title: operation + ' a team language', 
			message: "Are you sure to " + operation + " for language '" + lang_name + "' ?",
			buttons: ['Yes', 'Cancel'],
			callback: function(selected)
			{
				if(selected != 'Yes') return;

				if(operation == 'archive')
				{
					var win = window.open(site_url + "revision/zipAndDownLoad/" + rev_user_id, '_blank');
					if (win) {
					    //Browser has allowed it to be opened
					    win.focus();
					} else {
					    //Browser has blocked it
						open_simple_dialog
						({
							type: 'notification', title: operation + " language '" + lang_name + "'", 
							message: 'Please allow popups for this website'
						});
						
					    return;
					}
				}
				
				$.ajax
				({
			        type: "post",
			        cache: false,
			        url: site_url + "revision/doOperation",
			        dataType: "json",
			        data: { rev_id : "<?php echo $current_revision['id']; ?>", rev_user_id : rev_user_id,  operation: operation },
			        success: function (res)
			        {
			        	var alert = $('<div class="alert"></div>');
			            for(var i = 0; i < res.errors.length; i++) alert.append(res.errors[i] + "<br />");
			            
			            if(res.errors.length == 0)	alert.addClass("alert-success").append("Updated Successfully.");
			            else						alert.addClass("alert-danger");
			
						open_simple_dialog
						({
							type: 'notification', title: operation + " language '" + lang_name + "'", 
							message: alert[0].outerHTML,
							callback: function(e)
							{
								if(res.errors.length < 1)
								{
									
									location.href = site_url + "settings/approve";
								}
							}
						}); 
					},
			        error: function (xhr, ajaxOptions, thrownError) 
			        {
			            alert('Error occured while doing operation.');
			        },
			        async: false
			    });
			}
		});
	});
});
</script>
