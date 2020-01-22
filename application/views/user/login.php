<style>
p.error {
  display: inline-block;
  margin: 0;
  width: 200px;
}
div.login_line { 
  text-align: center; 
  padding: 3px 0;
}
div.login_line .input-group-addon {
	max-width: 40px;
	min-width: 40px;
	padding: 6px 0;
}

</style>
<div class="container-fluid" id="login_page" style="vertical-align: middle; background-image: url('<?php echo base_url('assets/img/background.jpg'); ?>'); background-size: 100% auto;" >
	<div class="row" style="text-align: center; height: 100%;" >
		<div class="col-lg-4 col-md-6 col-sm-7 col-xs-10" style="float: none; margin: 12% auto; padding: 0; box-shadow: 4px 4px 5px -2px #CCCCCC; text-align: center;">
			<div style="background-color: #212121; color: white; font-size: 57px; font-weight: bold; font-family: Bebas Neue; line-height: 1.3em;">CODEIGNITER 3</div>
			<div style="background-color: #4CB781; color: white; font-size: 20px; font-weight: bold; font-family: Bebas Neue; line-height: 1.1em; padding: 15px;">MULTILANGUAGE TRANSLATION<br />SUPPORT TOOL</div>
			<form method="post" action="<?php echo site_url('user/login'); ?>" style="padding: 15px 40px; background-color: #FAFAFA;">
				<div class="hidden-xs" >
				<?php if ($msg = $this->session->flashdata('login_message')) : ?>
				<p style="color: red; padding: 10px;"><?php echo $msg; ?></p>
				<?php endif; ?>
				<div class="input-group login_line">
					<span class="input-group-addon"><img src="<?php echo base_url('assets/img/mail.png'); ?>" /></span>
					<input tabindex="1" class="form-control" type="text" name="usermail" value="<?php echo set_value('usermail'); ?>" placeholder="Email" />
				</div>
				<?php if (form_error('usermail') != '' ) :?>  
				<?php echo form_error('usermail'); ?>
				<?php endif; ?>
				<div class="input-group login_line">
					<span class="input-group-addon"><img src="<?php echo base_url('assets/img/password.png'); ?>" /></span>
					<input tabindex="2" class="form-control" type="password" name="pwd" placeholder="Password" />
				</div>
				<?php if (form_error('pwd') != '' ) :?>  
				<?php echo form_error('pwd'); ?>
				<?php endif; ?>
					<div style="text-align: right; padding: 2px 0px 15px;">
						<a href="#" data-target="#pwdModal" data-toggle="modal" style="color: black; font-size: 9px;">Forgotten Password ?</a>
					</div>
					<input tabindex="3" type="submit" name="loginform" value="SIGN IN" style="border: 0; background-color: #4CB781; color: #9CE4BF; font-weight: bold; padding: 10px 50px; font-size: 11px; " />
				</div>
				<div class="visible-xs">
					Sorry, You can't login on small devices.
				</div>
			</form>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	$("#login_page").height(window.innerHeight - 72); 
});
</script>