<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta content="IE=edge" http-equiv="X-UA-Compatible" />
	<meta content="width=device-width, initial-scale=0.9" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<link type="image/x-icon" href="<?php echo base_url('assets'); ?>/ico/favicon.ico" rel="icon" />
	<link type="image/x-icon" href="<?php echo base_url('assets'); ?>/ico/favicon.ico" rel="shortcut icon" />
	<title><?php echo $title; ?></title>
	<script src="<?php echo base_url('assets/js/jquery-1.11.0.min.js'); ?>" ></script>
	<script src="<?php echo base_url('assets/js/jquery.cookie.js'); ?>" ></script>
	
	<!-- Bootstrap Framework -->
	<script src="<?php echo base_url('assets/bootstrap-3.1.1/dist/js/bootstrap.js'); ?>"></script>
	<script src="<?php echo base_url('assets/bootstrap-3.1.1/docs/assets/js/docs.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/fuelux/js/tree.js'); ?>"></script>
	
	<!-- For This Site Only -->
	<script type="text/javascript">var base_url = "<?php echo base_url('/'); ?>";</script>
	<script type="text/javascript">var site_url = "<?php echo site_url('/'); ?>";</script>

	<script src="<?php echo base_url('assets/js/common.js'); ?>"></script>
	
	<link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-3.1.1/dist/css/bootstrap.min.css'); ?>" type='text/css' media='all' />
	<link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-3.1.1/dist/css/bootstrap-theme.css'); ?>" type='text/css' media='all' />
	<link rel='stylesheet' href='<?php echo base_url('assets/bootstrap-3.1.1/docs/assets/css/docs.min.css'); ?>' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php echo base_url('assets/fuelux/css/tree-metronic.css'); ?>' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php echo base_url('assets/font-awesome/css/font-awesome.css'); ?>' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php echo base_url('assets/css/common.css'); ?>' type='text/css' media='all' />

	<?php if(isset($css) && is_array($css)) :?>
	<?php foreach ($css as $c) :?>
	<link rel='stylesheet' type='text/css' media='all' href='<?php echo base_url('assets/css/'.$c.'.css'); ?>' type='text/css' media='all' />
	<?php endforeach; ?>
	<?php endif; ?>

	<?php if(isset($js) && is_array($js)) :?>
	<?php foreach ($js as $j) :?>
	<script src="<?php echo base_url('assets/js/'.$j.'.js'); ?>" type="text/javascript"></script>
	<?php endforeach; ?>
	<?php endif; ?>
</head>
<body style="background: url('<?php echo base_url("assets/img/background.png"); ?>') repeat scroll 0 0 #F5F5F5; font-family: Lato; min-height : 100vh; overflow: auto; " >
<style>
<?php foreach (array('translator', 'proofer', 'moderator', 'statistics', 'actas') as $selector) : ?>
nav.navbar li.<?php echo $selector; ?> {
  background: url('<?php echo base_url('assets/img/'.$selector.'.png'); ?>') no-repeat scroll left center transparent;
  margin: 0 10px 0 55px;
	background-size: 25px auto;
}
nav.navbar li.<?php echo $selector; ?> a { padding-top: 10px; padding-bottom: 10px; }
nav.navbar li.<?php echo $selector; ?>.open, 
nav.navbar li.<?php echo $selector; ?>.current { background: url('<?php echo base_url('assets/img/'.$selector.'-selected.png'); ?>') no-repeat scroll left center transparent; background-size: 25px auto; }
<?php endforeach; ?>
nav.navbar li.open > a, li.current > a {
	background-color: transparent !important;
	color: white !important;
}
nav.navbar .menu-dropdown {}
nav.navbar .menu-dropdown ul.dropdown-menu { padding: 0; border-radius: 0; border: none; }
nav.navbar .menu-dropdown ul.dropdown-menu .caret1 {
  border-bottom: 6px solid white;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  display: inline-block;
  height: 0;
  left: 47%;
  position: absolute;
  top: -6px;
  vertical-align: middle;
  width: 0;
  z-index: 100;
}
nav.navbar .menu-dropdown ul.dropdown-menu li {
  border-bottom: 1px solid #F7F7F7;
  cursor: pointer;
  font-size: 13px;
  font-weight: bold;
  letter-spacing: -0.5px;
  line-height: 1em;
  padding: 11px 45px;
}
nav.navbar .menu-dropdown.actas ul.dropdown-menu li { padding: 7px 30px; }
nav.navbar .menu-dropdown ul.dropdown-menu li:hover, 
nav.navbar .menu-dropdown ul.dropdown-menu #language_list li.selected { background-color: black; color: white; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list {
  background-color: black;
  color: white;
  font-size: 11px;
  left: 100%;
  position: absolute;
  top: 0;
  width: 570px;
  -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
  box-shadow: 0 6px 12px rgba(0,0,0,.175);
  height: 169px;
  overflow: auto;
}
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block { width: 183px; float: left; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span { display: inline-block; padding: 9px; cursor: pointer; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span.unchanged {  }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span.inprogress { color: #878FF2; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span.completed { color: #2DA367; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span.uneditable { color: red; }
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span:hover, 
nav.navbar .menu-dropdown ul.dropdown-menu .lang-file-list .block span.selected { font-weight: bold; }

@media (min-width: 768px) and (max-width: 991px)   
{ 
	nav.navbar li.translator { margin-left: 40px; } 
	nav.navbar li.statistics { margin-left: 10px; margin-right: 0px; } 
	nav.navbar li.actas 	 { margin-left: 10px; margin-right: 0px; } 
	nav.navbar a.navbar-brand:before { content: 'CI3'; } 
	nav.navbar a.navbar-brand { margin: 0 5px 0 5px !important; width: 45px; padding: 15px 0;} 
}
@media (min-width: 992px) 
{ 
	nav.navbar a.navbar-brand { margin: 0 20px 0 20px !important; width: 190px; } 
	nav.navbar a.navbar-brand:before { content: 'CODEIGNITER 3'; } 
}  
</style>
<?php 
	$have_any_role = false;
	
	if($this->session->has_userdata('email') && $this->session->has_userdata('current_revision'))
	{
		$current_revision = $this->session->userdata('current_revision');
		
		if( !in_array($current_revision['status'], array( 'Created', 'Base Completed', 'Cleaning Up') ) )
		{
			if($this->session->userdata('is_global_admin') == 'yes' && !empty($current_revision['users']))
			{
				$have_any_role = true;
			}
			else 
			{
				foreach ($current_revision['users'] as $user)
				{
					if($user['translator_id'] == $this->session->userdata('id')) { $have_any_role = true; break; }
					if($user['proofer_id'] == $this->session->userdata('id')) { $have_any_role = true; break; }
					if($user['moderator_id'] == $this->session->userdata('id')) { $have_any_role = true; break; }
				}
			}
		}
	}

	if(!$have_any_role)
	{
		$menus = array();
	}
	else 
	{
		$menus = array(
			array( 'name' => 'Translate', 	'class' => 'translator',	'url' => 'transhome/translate' ),
			array( 'name' => 'Statistics', 	'class' => 'statistics',	'url' => 'transhome/statistics' ),
			array( 'name' => 'Act As', 		'class' => 'actas',			'url' => '#' )
		);
	}
?>
	<nav class="navbar navbar-default" role="navigation" style="border: none; margin: 0;">
		<div class="container-fluid" style="padding: 0;" >
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header" style="background-color: black;">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ci3-translator-navbar-collapse" style="margin-right: 30px;">
					<span class="sr-only">CI3</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo site_url(); ?>" style="color: white; font-size: 30px; font-family: Bebas Neue; text-align: center;"></a>
			</div>
			<div class="collapse navbar-collapse" id="ci3-translator-navbar-collapse" style="background-color: #4CB781;">
				<ul class="nav navbar-nav navbar-left">
					<?php foreach ($menus as $menu) : ?>
					<?php if(!$this->session->has_userdata('email')) continue; ?>
					<?php if($menu['name'] == 'Translate') : ?>
					<?php if($this->uri->segment(2) != 'translate') : ?>
					<li class="<?php echo $menu['class']; ?>" >
						<a href="<?php echo site_url($menu['url']); ?>" style="color: black;" >
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 14px;">SELECT</b><br />
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 12px;">SLAVE LANGUAGE</b>
						</a>
					</li>
					<?php else : ?>
					<li class="dropdown menu-dropdown <?php echo $menu['class']; ?> <?php if($this->uri->segment(2) == 'translate') echo 'current'; ?>" >
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color: black;" >
							<b id="translate_status" style="padding: 0 0 0 18px; line-height:1em; font-size: 14px;"></b><br />
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 12px;">- Select File</b>
						</a>
						<ul class="dropdown-menu">
							<span class="caret1"></span>
							<div id="language_list" style="height: 169px; overflow: auto;"></div>
							<div class="lang-file-list">
								<?php $count = count($master_files); ?>
								<?php while(TRUE) : ?>
								<div class="block">
									<?php for($i = 0; $i < ceil($count / 3); $i++) : ?>
									<?php if($lang_file = array_shift($master_files)) : ?>
									<span><?php echo $lang_file; ?></span>
									<?php else: ?>
									<?php break; ?>
									<?php endif; ?>
									<?php endfor; ?>
								</div>
								<?php if(!$lang_file) break; ?>
								<?php endwhile; ?>
							</div>						
						</ul>
					</li>
					<?php endif; ?>
					<?php elseif ($menu['name'] == 'Statistics') : ?>
					<li class="<?php echo $menu['class']; ?> <?php if($this->uri->segment(2) == 'statistics') echo 'current'; ?>" >
						<a href="<?php echo site_url($menu['url']); ?>" style="color: black;" >
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 14px;">
								SHOW
							</b><br />
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 14px;">
								STATISTICS
							</b>
						</a>
					</li>					
					<?php else : // Act As ?>
					<?php if($this->uri->segment(2) == 'translate') : ?>
					<li class="dropdown menu-dropdown <?php echo $menu['class']; ?>" >
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color: black;" >
							<b style="padding: 0 0 0 18px; line-height:1em; font-size: 14px;">
								ACT AS : 
							</b><br />
							<b id="actas" style="padding: 0 0 0 18px; line-height:1em; font-size: 12px;">
								- 
							</b>
						</a>
						<ul class="dropdown-menu">
							<span class="caret1"></span>
							<?php foreach (array(array('translator', 'TRANSLATOR'),	array('proofer', 'PROOF READER')) as $role) : ?>
							<?php foreach ($current_revision['users'] as $user) :?>
							<?php if(($this->session->userdata('is_global_admin') == 'yes' || $user['moderator_id'] == $this->session->userdata('id')) && $role[0] == 'translator') : ?>
							<li role="<?php echo $role[0]; ?>"><?php echo $role[1]; ?></li>
							<?php break; ?>
							<?php elseif($user[$role[0].'_id'] == $this->session->userdata('id')) : ?>
							<li role="<?php echo $role[0]; ?>"><?php echo $role[1]; ?></li>
							<?php break; ?>
							<?php endif; ?>
							<?php endforeach; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php endif; ?>
  					<?php endforeach; ?>
				</ul>
				<style>
					nav.navbar .navbar-right ul.dropdown-menu {
					  background-color: #2DA367;
					  border: 0 none;
					  border-radius: 0 0 0 0;
					  font-size: 11px;
					  width: 100%;
					  padding: 0px;
					}
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li {}
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li > a 
						{ color: #f5f5f5; background-image: none; background-color: transparent; padding: 10px 55px;}
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li > a:hover
						{ color: #f5f5f5; background-color: #4CB781; font-weight: bold;}
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li.current_revision 
						{ background-color: black; color: #2DA367; }
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li.current_revision > a,
					nav.navbar .navbar-right > .dropdown > ul.dropdown-menu > li.current_revision > a:hover
						{ background-color: black; color: #2DA367; cursor: default; font-family: Bebas Neue; font-size: 18px; font-weight: bold;}
				</style>
				<?php if($this->session->has_userdata('email')) :  // if logged in?> 
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown" style="background-color:#2DA367;">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color: white; font-size: 11px; padding: 15px 30px;">
							<img src="<?php echo base_url('assets/img/user.png'); ?>" style="width:15px" />
							<span style="padding: 0 10px;">Welcome , <?php echo $this->session->userdata('username'); ?></span><span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php if($this->session->has_userdata('current_revision')) : ?>
							<?php 
								$current_revision = $this->session->userdata('current_revision');
								$related_revisions = $this->session->userdata('related_revisions');
							?>	
							<li class="current_revision" >
								<a href="#">
									<span>Rev : <?php echo $current_revision['revision_name']; ?></span>
								</a>
							</li>
							<li><a href="<?php echo site_url('settings'); ?>"><span>Control Panel</span></a></li>
							<?php else: ?>
							<?php if($this->session->userdata('is_global_admin') == 'yes') : ?>
							<li><a href="<?php echo site_url('settings'); ?>"><span>Control Panel</span></a></li>
							<?php endif; ?>
							<?php endif; ?>
							<li><a href="<?php echo site_url('user/logout'); ?>"><span>Logout</span></a></li>
						</ul>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</nav>
	<script>
//	$(document).ready(function()
//	{
//	});
	</script>