<style>
#home_page .linkage {
  display: inline-block;
  left: 0;
  position: absolute;
  top: 23%;
  width: 100%;
	text-align: center;
}
#home_page .linkage span {
  color: #4cb781;
  display: inline-block;
  font-size: 21px;
  letter-spacing: 2px;
  line-height: 5px;
  overflow: hidden;
  text-align: justify;
}
#home_page .step { display: inline-block; width: 18%; }
#home_page .step div.step-circle {
  background-color: #4cb781;
  border: 2px solid #f5f5f5;
  border-radius: 50%;
  box-shadow: 0 0 0 2px #4cb781;
  display: inline-block;
  height: 75px;
  width: 75px;
}
#home_page .step div.step-circle img {	padding-top: 28%; width: 53%; }
#home_page .step div.title {
  font-size: 16px;
  font-weight: bold;
  line-height: 1em;
  margin-top: 12px;
}
#home_page .step div.stitle 
{
  font-size: 10px;
  line-height: 1em;
	padding: 0 15px;
}
#home_page .sub-title {
  font-family: Bebas Neue;
  font-size: 24px;
  font-weight: bold;
  margin: 20px 0 40px;
  text-align: center;
}
#home_page .customize_table {
	margin: 0; 
	border-left: 1px solid black;
	border-right: 1px solid black;
}
#home_page .table1 {
	border-left: 1px solid #DDDDDD;
	border-right: 1px solid #DDDDDD;
}
#home_page .customize_table tr th, #home_page .customize_table tr td {
	font-size: 11px; 
	vertical-align: middle;
}
#home_page .customize_table tr th {
  background-color: black;
  border: medium none !important;
  color: white;
  padding: 13px 5px 13px 15px !important;
  text-transform: uppercase;
}
#home_page .customize_table tr td {
	border-top: none;
	border-bottom: 1px solid #DDDDDD;
	padding: 1px 5px 1px 15px !important;
}
#home_page .customize_table tr td input {
	width: 100%; color: #2C9CDD;
	border: 1px solid transparent;
	font-size: 14px;
	background-color: transparent;
}
#home_page .customize_table tr td input:focus {
	border: 1px solid #dddddd;
}
#home_page .customize_table tr.empty {
	background-color: #F2DEDE;	
}
#home_page .customize_table tr.empty input {
	color: black;
	font-size: 12px;	
}
#home_page .basic_table tr {cursor: pointer; }
#home_page .basic_table tr td { font-size: 10px; }
#home_page .basic_table tr:hover, #home_page .basic_table tr.selected {background-color: white; }
#home_page .basic_table tr td span {
  border-radius: 3px;
  display: inline-block;
  margin: 0 0 1px;
  padding: 3px 0;
  text-align: center;
  width: 87%;
}
#home_page .basic_table tr td span.completed { background-color: #2da367; color: #f5f5f5; }
#home_page .basic_table tr td span.inprogress { background-color: blue; color: #f5f5f5; }
#home_page .basic_table tr td span.unchanged { background-color: red; color: #f5f5f5; }

@media (min-width: 768px) and (max-width: 991px)   
{
	#home_page .linkage span:nth-child(1) {margin: 0 6% 0 15%; width: 6%;} 
	#home_page .linkage span:nth-child(2) {margin: 0 6%; width: 6%;} 
	#home_page .linkage span:nth-child(3) {margin: 0 15% 0 6%; width: 6%;} 
}

@media (min-width: 992px) 
{ 
	#home_page .linkage span:nth-child(1) {margin: 0 4% 0 13%; width: 10%; } 
	#home_page .linkage span:nth-child(2) {margin: 0 4%; width: 10%; } 
	#home_page .linkage span:nth-child(3) {margin: 0 13% 0 4%; width: 10%; } 
}
</style>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="home_page" style="height: 100%;">
	<div class="row" style="height: 100%;">
		<div class="sub-title">Welcome <?php echo $this->session->userdata('username'); ?> !</div>
		<div style="text-align: center;  position: relative;">
			<?php 
				$steps = array
				(
					array('select-step.png', '1. SELECT', 'SLAVE LANGUAGE &amp; FILE'), 
					array('check-step.png', '2. CHECK', 'LANGUAGE FILES &amp; AUTO CORRECT'), 
					array('view-step.png', '3. VIEW', 'TRANSLATION &amp; STATISTICS'), 
					array('save-step.png', '4. SAVE', 'TO LANGUAGE FOLDER &amp; ENJOY') 
				); 
			?>
			<?php foreach ($steps as $step) : ?>
			<div class="step">
				<div class="step-circle" >
					<img src="<?php echo base_url('assets/img/'.$step[0]); ?>" <?php echo $step[1] == '1. SELECT' ? 'style="width: 56%; margin-left: 8%; padding-top: 29%;"' : ''; ?> />
				</div>
				<div class="title"><?php echo $step[1]; ?></div>
				<div class="stitle"><?php echo $step[2]; ?></div>
			</div>
			<?php endforeach; ?>
			<div class="linkage" >
					<span>--------------</span>
					<span>--------------</span>
					<span>--------------</span>
			</div>
		</div>
		<br />
		<br />
		<?php if(isset($statistics) && !empty($statistics)) : ?>
		<?php 
			$langMap = array();
			foreach($languages as $language) $langMap[$language['id']] = $language['name'];
		?>
		<div class="sub-title" style="margin-bottom: 10px;">CURRENT STATISTICS</div>
		<div style="height: 46%; overflow: auto;">
			<div class="table-responsive" style="font-weight: bold; font-size: 12px; color: gray; width: 400px; margin: auto;">
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
							<td width="26%" style="font-size: 12px;"><?php echo $langMap[$statis['language_id']]; ?></td>
							<td width="24%">
								<span style="color:#2da367;">Completed</span>
								<span style="color:blue;">In Progress</span>
								<span style="color:red;">Unchanged</span>
							</td>
							<td width="25%">
								<span class="completed"><?php echo $statis['simple_status']['translator']['Completed']; ?> file(s)</span>
								<span class="inprogress"><?php echo $statis['simple_status']['translator']['In Progress']; ?> file(s)</span>
								<span class="unchanged"><?php echo $statis['simple_status']['translator']['Unchanged']; ?> file(s)</span>
							</td>
							<td width="25%">
								<span class="completed"><?php echo $statis['simple_status']['proofer']['Completed']; ?> file(s)</span>
								<span class="inprogress"><?php echo $statis['simple_status']['proofer']['In Progress']; ?> file(s)</span>
								<span class="unchanged"><?php echo $statis['simple_status']['proofer']['Unchanged']; ?> file(s)</span>
							</td>
						</tr>
				  		<?php endforeach; ?>
			  		</tbody>
				</table>
			</div>
			<div style="text-align: right; width: 400px; margin: auto; ">
				<a href="<?php echo site_url('transhome/statistics'); ?>" style="background-color:#4CB781; margin-top: 10px; color: white; border: none; padding: 7px 15px; display: inline-block; font-size: 12px; ">CHECK DETAILS</a>
			</div>
		</div>
		<?php elseif(isset($statistics) && empty($statistics)) : ?>
		<div style="text-align: center; font-weight: bold;">
			No language role is assigned to you at current revision.<br />
			Please wait for the administrator to assign a language to you.
		</div>
		<?php else: ?>
		<div style="text-align: center; font-weight: bold;">
			You are attending to no revision.<br />
			Please wait for the administrator to include you to any revision.
		</div>
		<?php endif; ?>
	</div>
</div>
<script>
jQuery(document).ready(function() 
{
});
</script> 