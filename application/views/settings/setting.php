<?php $current_revision = $this->session->userdata('current_revision'); ?>
<style>
.dashboard { background-color: #F5F5F5; padding: 1px 0 0; }
.dashboard ul.nav-tabs {width: 230px; background-color: #2DA367; text-align: center; float: left; }
.dashboard ul.nav-tabs li.item {border-top: 1px solid #26975E; margin: 0; text-align: left;}
.dashboard ul.nav-tabs li.item a {
  background-color: #2da367;
  background-image: none;
  border: medium none;
  border-radius: 0;
  color: #f5f5f5;
  display: table-cell;
  font-family: Lato Regular;
  font-size: 10px;
  height: 42px;
  margin: 0;
  outline: medium none;
  padding: 0 0 0 40px;
  text-transform: uppercase;
  vertical-align: middle;
  width: 230px;
}
.dashboard ul.nav-tabs li span.caret { position: absolute; right: 24px; top: 46%; cursor: pointer; }
.dashboard ul.nav-tabs li span.caret-left  { border-top: 4px solid transparent; border-bottom: 4px solid transparent; border-right: 4px solid;}
.dashboard ul.nav-tabs li span.caret-right { border-top: 4px solid transparent; border-bottom: 4px solid transparent; border-left: 4px solid;}
.dashboard ul.nav-tabs li span.caret-drop  { border-left: 2px solid transparent; border-right: 2px solid transparent; }
.dashboard ul.nav-tabs li.item a img { width: 22px; margin-right: 12px; }
.dashboard ul.nav-tabs li.item a:hover, 
.dashboard ul.nav-tabs li.item.active > a {font-weight: bold; background-color: #4CB781;}
.dashboard ul.nav-tabs li.item.open ul.dropdown-menu {
  background-color: black;
  border: medium none;
  border-radius: 0;
  box-shadow: none;
  float: none;
  padding: 0;
  position: inherit;
min-width: auto;
}
.dashboard ul.nav-tabs li.item ul.dropdown-menu li.item a { padding-left: 48px; }
.dashboard ul.nav-tabs li.item.open ul.dropdown-menu li { background-color: #2DA367 !important; cursor: pointer;}
.dashboard .tab-content { width: 100%; }
@media (min-width: 768px) and (max-width: 991px) { .dashboard .tab-content { padding: 30px 3%; } }
@media (min-width: 992px) { .dashboard .tab-content { padding: 30px 6%; } }

</style>
<div class="container-fluid" style="background-color: #f5f5f5; height:100%; ">
	<div class="row" style="">
		<div class="dashboard col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: table;">
			<ul class="nav nav-tabs nav-pills nav-stacked" id="dashtabs" style="display: table-cell; ">
				<li style="height: 68px; font-size: 20px; color: #2DA367; background-color: black; font-family: Bebas Neue; font-weight: bold; padding: 15px 0 13px 0; line-height: 1em; ">
					<span class="menu-text">Administration<br />Dashboard</span>
					<span class="caret caret-left"></span>
				</li>
				<?php if(isset($rev_control)) : ?>
				<li class="item dropdown"  >
					<a class="dropdown-toggle" href="#" >
						<img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">Rev - <?php echo $current_revision['revision_name']; ?></span> 
						<span class="caret caret-drop"></span>
					</a>
				    <ul class="dropdown-menu">
				    	<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
						<li class="item <?php echo $tab_name == 'base' ? 'active' : ''; ?>" ><a page="#base" ><img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">BASE INFO</span></a></li>
						<?php if(!in_array($current_revision['status'], array('Created'))) : ?>
						<li class="item <?php echo $tab_name == 'cleanup' ? 'active' : ''; ?>" ><a page="#cleanup" ><img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">CLEAN UP</span></a></li>
						<?php endif; ?>
						<?php if(!in_array($current_revision['status'], array('Created', 'Base Completed', 'Cleaning Up'))) : ?>
						<li class="item <?php echo $tab_name == 'page' ? 'active' : ''; ?>" ><a page="#page" ><img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">PATH &amp; LINK TO PAGES</span></a></li>
						<?php endif; ?>
						<?php endif; ?>
						<?php if(!in_array($current_revision['status'], array('Created', 'Base Completed', 'Cleaning Up'))) : ?>
						<li class="item <?php echo $tab_name == 'team' ? 'active' : ''; ?>" ><a page="#team" ><img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">Team Configuration</span></a></li>
						<li class="item <?php echo $tab_name == 'approve' ? 'active' : ''; ?>" ><a page="#approve" ><img src="<?php echo base_url('assets/img/setting.png'); ?>" /><span class="menu-text">Approve &amp; Publish</span></a></li>
						<?php endif; ?>
				    </ul>
				</li>
				<?php endif; ?>
				<li class="item"><a href="#users" data-toggle="tab"><img src="<?php echo base_url('assets/img/users.png'); ?>" /><span class="menu-text">MY USERS</span></a></li>
				<li class="item"><a href="#revisions" data-toggle="tab"><img src="<?php echo base_url('assets/img/revision.png'); ?>" /><span class="menu-text">MY REVISIONS</span></a></li>
				<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
				<li class="item"><a href="#languages" data-toggle="tab"><img src="<?php echo base_url('assets/img/publish.png'); ?>" /><span class="menu-text">Languages</span></a></li>
				<?php endif; ?>
			</ul>
			<div class="tab-content" style="height: 100%; display: table-cell; vertical-align: top;">
				<?php if(isset($rev_control)) : ?>
				<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
				<div class="tab-pane <?php echo $tab_name == 'base' ? 'active' : ''; ?>" id="base" style="height: 100%" ><?php echo $basic_panel; ?></div>
				<?php if(!in_array($current_revision['status'], array('Created'))) : ?>
				<div class="tab-pane <?php echo $tab_name == 'cleanup' ? 'active' : ''; ?>" id="cleanup" style="height: 100%" ><?php echo $cleanup_panel; ?></div>
				<?php endif; ?>
				<?php if(!in_array($current_revision['status'], array('Created', 'Base Completed', 'Cleaning Up'))) : ?>				
				<div class="tab-pane <?php echo $tab_name == 'page' ? 'active' : ''; ?>" id="page" style="height: 100%" ><?php echo $path_panel; ?></div>
				<?php endif; ?>
				<?php endif; ?>
				<?php if(!in_array($current_revision['status'], array('Created', 'Base Completed', 'Cleaning Up'))) : ?>
				<div class="tab-pane <?php echo $tab_name == 'team' ? 'active' : ''; ?>" id="team" style="height: 100%" ><?php echo $team_panel; ?></div>
				<div class="tab-pane <?php echo $tab_name == 'approve' ? 'active' : ''; ?>" id="approve" style="height: 100%" ><?php echo $approval_panel; ?></div>
				<?php endif; ?>
				<?php endif; ?>
				<div class="tab-pane" id="users" style="height: 100%" ><?php echo $users_panel; ?></div>
				<div class="tab-pane" id="revisions" style="height: 100%" ><?php echo $revision_panel; ?></div>
				<?php if($this->session->userdata('is_global_admin') == 'yes'): ?>
				<div class="tab-pane" id="languages" style="height: 100%" ><?php echo $languages_panel; ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	$(".dashboard ul.nav-tabs li.item.dropdown").click(function(ev)
	{
		if($(this).hasClass('open')) $(this).removeClass('open');
		else $(this).addClass('open');
	});

	$(".dashboard ul.nav-tabs > li.item:gt(0)").click(function(ev)
	{
		$(".dashboard ul.nav-tabs li.item.dropdown ul.dropdown-menu li").removeClass('active');
	});
	
	$(".dashboard ul.nav-tabs li.item.dropdown ul.dropdown-menu li").click(function(ev)
	{
		ev.stopImmediatePropagation();

		var pageId = $(this).find('a').attr('page').slice(1);
		var dashboard = $(this).parents(".dashboard")[0];
		$(dashboard).find("#dashtabs li.item").removeClass('active');
		$(this).addClass('active');

		$(dashboard).find('.tab-content div.tab-pane').removeClass('active');
		$(dashboard).find('.tab-content div.tab-pane#' + pageId).addClass('active');
	});

	$('.dashboard ul.nav-tabs li').on('click', 'span.caret-left', function()
	{
		$('.dashboard ul.nav-tabs').animate({'width': '55px'});
		$(this).removeClass('caret-left').addClass('caret-right');		
		$('.dashboard ul.nav-tabs li span.menu-text').hide();
		$('.dashboard ul.nav-tabs > li.item > a').animate({'padding-left': '10px'});
		$('.dashboard ul.nav-tabs li.item ul.dropdown-menu li.item a').animate({'padding-left': '18px'});
		$('.dashboard ul.nav-tabs li span.caret-drop').css('right', '10px');
	});

	$('.dashboard ul.nav-tabs li').on('click', 'span.caret-right', function()
	{
		$('.dashboard ul.nav-tabs').animate({'width': '230px'});
		$(this).removeClass('caret-right').addClass('caret-left');		
		$('.dashboard ul.nav-tabs li span.menu-text').show();
		$('.dashboard ul.nav-tabs > li.item > a').animate({'padding-left': '40px'});
		$('.dashboard ul.nav-tabs li.item ul.dropdown-menu li.item a').animate({'padding-left': '48px'});
		$('.dashboard ul.nav-tabs li span.caret-drop').css('right', '24px');
	});

	var h = window.innerHeight - 130; 
	$(".dashboard > div.tab-content").height(h);
	$(".dashboard > ul.nav-tabs").height(h + 60);
	$(".dashboard > .tab-content .table-responsive").height($('body').height() - 230);

	$('#dashtabs a[href="#<?php echo $tab_name; ?>"]').tab('show');
});
</script>