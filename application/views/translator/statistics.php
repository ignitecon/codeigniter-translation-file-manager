<style>
#statistics_page .customize_table { margin: 0; border-left: 1px solid black; border-right: 1px solid black; }
#statistics_page .table1 { border-left: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD; }
#statistics_page .customize_table tr th, #statistics_page .customize_table tr td { font-size: 11px; vertical-align: middle; }
#statistics_page .customize_table tr th { background-color: black; border: medium none !important; color: white; padding: 13px 5px 13px 15px !important; text-transform: uppercase; }
#statistics_page .customize_table tr td { border-top: none; border-bottom: 1px solid #DDDDDD; padding: 1px 5px 1px 15px !important; }
#statistics_page .customize_table tr td input { width: 100%; color: #2C9CDD; border: 1px solid transparent; font-size: 14px; background-color: transparent; }
#statistics_page .customize_table tr td input:focus { border: 1px solid #dddddd; }
#statistics_page .customize_table tr.empty { background-color: #F2DEDE; }
#statistics_page .customize_table tr.empty input { color: black; font-size: 12px; }
#statistics_page .basic_table tr {cursor: pointer; }
#statistics_page .basic_table tr td { font-size: 10px; }
#statistics_page .basic_table tr:hover, #statistics_page .basic_table tr.selected {background-color: white; }
#statistics_page .basic_table tr td span { border-radius: 3px; display: inline-block; margin: 0 0 1px; padding: 3px 0; text-align: center; width: 87%; }
#statistics_page .basic_table tr td span.completed { background-color: #2da367; color: #f5f5f5; }
#statistics_page .basic_table tr td span.inprogress { background-color: blue; color: #f5f5f5; }
#statistics_page .basic_table tr td span.unchanged { background-color: red; color: #f5f5f5; }
#statistics_page .detail_table tr td:nth-child(1) { cursor: pointer; }
#statistics_page .detail_table tr td:nth-child(1):hover { color: #2a48aa; text-decoration: underline; }
</style>
<?php 
	$langMap = array();
	foreach($languages as $language) $langMap[$language['id']] = $language['name'];
?>
<div class="container-fluid" id="statistics_page" style="background-color: #F5F5F5; height: 100%;" >
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height: 100%;">
		<div class="row" style="height: 100%;">
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="height: 100%;">
				<div style="padding: 20px 0 10px; font-size: 22px;  font-weight: bold; font-family: Bebas Neue;">AVAILABLE LANGUAGES</div>
				<div class="table-responsive" style="font-weight: bold; font-size: 12px; height: 74%; overflow: auto; color: gray;">
					<table class="customize_table table table-bordered table1 basic_table" >
				  		<thead>
				  			<tr>
				  				<th width="26%">LANGUAGE</th>
				  				<th width="24%">STATUS</th>
				  				<th width="25%">TRANSLATE</th>
				  				<th width="25%">PROOF READ</th>
				  			</tr>
				  		</thead>
				  		<tbody>
				  			<?php
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
					  		<?php foreach ($statistics as $statis) : ?>
							<tr lang_id="<?php echo $statis['language_id']; ?>">
								<td style="font-size: 12px;"><?php echo $langMap[$statis['language_id']]; ?></td>
								<td>
									<span style="color: #2da367;">Completed</span>
									<span style="color: blue;">In Progress</span>
									<span style="color: red;">Unchanged</span>
								</td>
								<td>
									<span class="completed"><?php echo $statis['simple_status']['translator']['Completed']; ?> file(s)</span>
									<span class="inprogress"><?php echo $statis['simple_status']['translator']['In Progress']; ?> file(s)</span>
									<span class="unchanged"><?php echo $statis['simple_status']['translator']['Unchanged']; ?> file(s)</span>
								</td>
								<td>
									<span class="completed"><?php echo $statis['simple_status']['proofer']['Completed']; ?> file(s)</span>
									<span class="inprogress"><?php echo $statis['simple_status']['proofer']['In Progress']; ?> file(s)</span>
									<span class="unchanged"><?php echo $statis['simple_status']['proofer']['Unchanged']; ?> file(s)</span>
								</td>
							</tr>
					  		<?php endforeach; ?>
				  		</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" style="height: 100%;">
				<div style="padding: 20px 0 10px; font-size: 22px;  font-weight: bold; font-family: Bebas Neue;">
					<span id="selected_language" style="color:#4BB881; text-transform: uppercase;">&lt;Selected Language&gt;</span> 
					<span id="errors_number"></span>
				</div>
				<div class="table-responsive" style="font-weight: bold; font-size: 12px; height: 74%; overflow: auto; color: gray; margin-bottom: 40px;">
					<table class="customize_table table table-bordered table1 detail_table" >
				  		<thead>
				  			<tr>
				  				<th width="30%">Language File</th>
				  				<th width="20%" style="padding: 13px 0 !important;">Translation Progress</th>
				  				<th width="25%">Translate</th>
				  				<th width="25%">Proof Read</th>
				  			</tr>
				  		</thead>
				  		<tbody>
				  			<?php foreach ($master_files as $lang_file) : ?>
				  			<tr>
				  				<td><?php echo $lang_file; ?></td>
				  				<td style="background-color: #FFEBED; padding: 0 !important;">
				  					<div style="width: 0%; height: 34px; background-color: #4CB781; position: relative;">
					  					<span style="position: absolute; color: white; padding: 8px;"></span>
				  					</div>
				  				</td>
				  				<td></td>
				  				<td></td>
				  			</tr>
				  			<?php endforeach;?>
				  		</tbody>
					</table>
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">
var repo_status_map = {};
<?php foreach ($statistics as $statis) : ?>
repo_status_map['<?php echo $statis['language_id']; ?>'] = {};
<?php foreach ($statis['status'] as $entry) : ?>
repo_status_map['<?php echo $statis['language_id']; ?>']['<?php echo $entry['path']; ?>'] = {};
<?php foreach ($entry as $key => $val) : ?>
repo_status_map['<?php echo $statis['language_id']; ?>']['<?php echo $entry['path']; ?>']['<?php echo $key; ?>'] = '<?php echo $val; ?>';
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>

jQuery(document).ready(function() 
{
	$("#statistics_page .basic_table tbody tr").click(function()
	{
		$("#statistics_page .basic_table tbody tr").removeClass('selected');
		$(this).addClass('selected');
	
		var lang_selected_id = $(this).attr('lang_id');
		var lang_selected = $(this).find('td:eq(0)').html();
	
		$.each($('#statistics_page .detail_table tbody tr'), function(index, tr)
		{
			var lang_file = $(tr).find('td:eq(0)').html().replace(/(\/|\\)/, '');
			var secondcol = $(tr).find('td:eq(1)');
			var thirdcol = $(tr).find('td:eq(2)');
			var fourthcol = $(tr).find('td:eq(3)');
			var info = repo_status_map[lang_selected_id][lang_file];
			
			if(!info)
			{
				secondcol.find('div').css('width', 0);
				secondcol.find('span').html('0.0%');
				thirdcol.html('Unchanged');
				fourthcol.html('Unchanged');
			}
			else
			{
				var percent = parseInt(info['total_empty_keys']) * 100 / parseInt(info['total_keys']);
				percent = Math.round((100 - percent) * 100) / 100;
				secondcol.find('div').css('width', percent + "%");
				secondcol.find('span').html(percent + "%");
				thirdcol.html(info['translator_status'] + "<br />" + "Keys : " + info['translator_keys'] + ", Words : " + info['translator_words']);
	
				if(info['proofer_status'] == '') fourthcol.html('Unchanged');
				else 
				{
					fourthcol.html(info['proofer_status']);
				}
			}
		});	
	
		$("#statistics_page #selected_language").html(lang_selected);
	});

	$("#statistics_page .basic_table tbody tr:eq(0)").click();

	$("#statistics_page .detail_table tr").find("td:eq(0)").click(function()
	{
		var selected_lang_id = $("#statistics_page .basic_table tbody tr.selected").attr('lang_id');
		var selected_lang_file = $(this).html(); //.replace(/(\/|\\)/, '');

		$.cookie('selected_lang_id', selected_lang_id);
		$.cookie('selected_lang_file', selected_lang_file);
		
		location.href = "<?php echo site_url('transhome/translate'); ?>";
	});
});
</script> 
