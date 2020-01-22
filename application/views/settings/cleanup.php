<style>
#cleanup_page .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
#cleanup_page .basic_table .dropdown ul.dropdown-menu {
	max-height: 200px;
	overflow: auto;
	min-width: 50px; 
}
#cleanup_page .basic_table .dropdown ul.dropdown-menu li a {
	font-size: 11px;
}
#cleanup_page .basic_table .dropdown button.dropdown-toggle {
	font-size: 11px; padding: 2px 11px;
}
#cleanup_page .basic_table tbody tr td.languages { }
#cleanup_page .basic_table tbody tr td.languages > span {
  background-color: #ECE9D8;
  border-radius: 4px 4px 4px 4px;
  display: inline-block;
  margin: 0 0px 2px 2px;
  padding: 2px 6px 2px 10px;
	float: left;
}
#cleanup_page .basic_table thead tr th { text-align: center; }
#cleanup_page .basic_table thead tr th:nth-child(2) { text-align: left; }
#cleanup_page .basic_table tbody tr td:nth-child(1) { text-align: center; }
#cleanup_page .basic_table tbody tr td:nth-child(3),
#cleanup_page .basic_table tbody tr td:nth-child(4),
#cleanup_page .basic_table tbody tr td:nth-child(5) { text-align: right; padding-right: 7%; }

</style>
<?php $current_revision = $this->session->userdata('current_revision'); ?>
<div style="height: 100%;" id="cleanup_page">
	<div class="sub-title">CLEAN UP</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th>#</th>
	  				<th>Language File</th>
	  				<th>Total Keys</th>
	  				<th>Added Keys</th>
	  				<th>Deleted Keys</th>
	  			</tr>
	  		</thead>
	  		<tbody>
	  			<?php $count = 1; ?>
				<?php foreach ($master_files as $lang_file) : ?>
	  			<tr>
					<td><?php echo $count++; ?></td>		
					<td><?php echo $lang_file['name']; ?></td>		
					<td><?php echo $lang_file['total_keys']; ?></td>		
					<td><?php echo $lang_file['added_keys']; ?></td>		
					<td><?php echo $lang_file['deled_keys']; ?></td>		
	  			</tr>
				<?php endforeach; ?>
	  		</tbody>
		</table>
	</div>
	<div style="text-align: right; ">
  		<?php if(in_array($current_revision['status'], array('Created', 'Base Completed'))) : ?>
		<button id='cleanup_start' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 10px; width: 130px; font-family: Lato Regular; font-size: 12px;" >START CLEAN UP</button>
		<?php endif; ?>
  		<?php if(in_array($current_revision['status'], array('Cleaning Up'))) : ?>
		<button id='cleanup_end' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 0px; width: 130px; font-family: Lato Regular; font-size: 12px;" >FINISH CLEAN UP</button>
		<?php endif; ?>
  		<?php if(in_array($current_revision['status'], array('Clean Completed'))) : ?>
		<button style="background-color: #f5f5f5; color: #4cb781; border: 1px solid black;; padding: 10px 0px; width: 140px; font-family: Lato Regular; font-size: 12px;" disabled >CLEAN COMPLETED</button>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
function doCleanUp(operation)
{
	$.ajax
	({
        type: "post",
        cache: false,
        url: site_url + "revision/" + operation + "CleanUp",
        dataType: "json",
        data: {  },
        success: function (res) 
        {
        	var alert = $('<div class="alert"></div>');
            if(res.errors.length == 0)
            {
            	alert.addClass("alert-success").append("Clean up " + operation + "ed successfully.");
            }
            else
            {
            	alert.addClass("alert-danger");
            	
	            for(var i = 0; i < res.errors.length; i++)
	            {
	            	alert.append(res.errors[i] + "<br />");
	            }
            }

            var opt = operation.toLowerCase().replace(/\b[a-z]/g, function(letter) { return letter.toUpperCase(); });
            
			open_simple_dialog
			({
				type: 'notification', title: opt + ' Clean Up', message: alert[0].outerHTML,
				callback: function(e)
				{
					if(res.errors.length < 1) location.href = site_url + "settings/cleanup";
				}
			}); 
		},
        error: function (xhr, ajaxOptions, thrownError) 
        {
            alert('Error occured while clean up.');
        },
        async: false
    });
}

$(document).ready(function()
{
	$("#cleanup_page #cleanup_start").click(function(){ doCleanUp('start'); });
	$("#cleanup_page #cleanup_end").click(function(){ doCleanUp('finish'); });
});
</script>
