<?php $current_revision = $this->session->userdata('current_revision'); ?>

<style>
#path_page_url .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 15px;
}
#path_page_url .basic_table .dropdown ul.dropdown-menu {
	max-height: 200px;
	overflow: auto;
	min-width: 50px; 
}
#path_page_url .basic_table .dropdown ul.dropdown-menu li a {
	font-size: 11px;
}
#path_page_url .basic_table .dropdown button.dropdown-toggle {
	font-size: 11px; padding: 2px 11px;
}
#path_page_url .basic_table tbody tr td div.path_url { border: 1px solid transparent; }
#path_page_url .basic_table tbody tr td div.path_url:before { content: "<?php echo $current_revision['target_ci_url'].'/'; ?>"; }

</style>
<div style="height: 100%; " id="path_page_url">
	<div class="sub-title">PATH &amp; LINK TO PAGES</div>
	<div class="table-responsive" style="height: 71%; overflow: auto; margin-bottom: 20px; ">
		<table class="table table-bordered basic_table" style="margin: 0;">
	  		<thead>
	  			<tr>
	  				<th width="30px">#</th>
	  				<th width="130px" style="white-space: nowrap;">Path</th>
	  				<th>Link To Pages</th>
	  				<th width="50px" >Actions</th>
	  			</tr>
	  		</thead>
	  		<tbody>
				<?php $count = 1; ?>
				<?php foreach ($lang_file_list as $path) : ?>
	  			<tr>
					<td><?php echo $count++; ?></td>		
					<td><?php echo $path; ?></td>
					<td>
						<?php if(isset($path_page_urls[$path])) : ?>
						<?php foreach ($path_page_urls[$path] as $path_url) : ?>
						<div class="path_url"><?php echo $path_url; ?></div>
						<?php endforeach; ?>
						<?php endif; ?>
					</td>		
					<td>
						<div class="dropdown">
							<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
								 Do <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu" >
						    	<li><a tabindex="-1" href="#" data-toggle="modal" data-target="#pathURLModal" >Edit</a></li>
						  	</ul>
						</div>
					</td>
	  			</tr>
				<?php endforeach; ?>
	  		</tbody>
		</table>
	</div>
	<div style="text-align: right; ">
		<button id='path_save_page_url' style="background-color: #4cb781; color: #f5f5f5; border: medium none; padding: 10px 30px; width: 130px; font-family: Lato Regular; font-size: 12px;" >SAVE</button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#path_page_url #path_save_page_url").click(function()
	{
		var path_url_list = [];
		$.each($("#path_page_url .basic_table tbody tr"), function(index, tr)
		{
			var url_list = [];

			$.each($(tr).find("td:eq(2) > div"), function(index, div)
			{
				url_list.push($(div).html());
			});

			path_url_list.push({path: $(tr).find("td:eq(1)").html(), url_list: url_list});
		});
		
		$.ajax
		({
	        type: "post",
	        cache: false,
	        url: site_url + "revision/savePathPageURLs",
	        dataType: "json",
	        data: { rev_id : "<?php echo $current_revision['id']; ?>", path_url_list: path_url_list },
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

				open_simple_dialog({type: 'notification', title: 'Update Path &amp; Link To Pages', message: alert[0].outerHTML}); 
			},
	        error: function (xhr, ajaxOptions, thrownError) {
	            alert('Error occured while saving path and page URL information.');
	        },
	        async: false
	    });
	});
});
</script>
