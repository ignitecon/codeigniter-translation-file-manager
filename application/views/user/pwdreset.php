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
<div class="container" style="vertical-align: middle; width: 100%; height: 90%; display: table; background-image: url('<?php echo base_url('assets/img/background.jpg'); ?>'); background-size: 100% auto;" >
	<div class="row" style="text-align: center; display: table-cell; vertical-align: middle;" >
		<div class="col-lg-4 col-md-6 col-sm-10 col-xs-10" style="float: none; margin: 30px auto; padding: 0; box-shadow: 4px 4px 5px -2px #CCCCCC; text-align: center;">
			<div style="background-color: #212121; color: white; font-size: 57px; font-weight: bold; font-family: Bebas Neue; line-height: 1.3em;">CODEIGNITER 3</div>
			<div style="background-color: #4CB781; color: white; font-size: 20px; font-weight: bold; font-family: Bebas Neue; line-height: 1.1em; padding: 15px;">PASSWORD RESET</div>
			<?php if ($msg = $this->session->flashdata('pwd_reset_success')) : ?>
			<div style="padding: 10px 0;">Reset Password Success ! <br /> Please log in with new password. <a href="<?php echo site_url(); ?>">Log in</a></div>
			<?php else : ?>
			<form method="post" action="" style="padding: 15px 40px; background-color: #FAFAFA;">
				<?php if ($msg = $this->session->flashdata('pwd_reset_message')) : ?>
				<p style="color: red; padding: 10px;"><?php echo $msg; ?></p>
				<?php endif; ?>
				<div class="input-group login_line">
					<span class="input-group-addon"><img src="<?php echo base_url('assets/img/password.png'); ?>" /></span>
					<input tabindex="1" class="form-control" type="password" name="pwd" value="<?php echo set_value('pwd'); ?>" placeholder="Password" />
				</div>
				<?php if (form_error('pwd') != '' ) :?>  
				<?php echo form_error('pwd'); ?>
				<?php endif; ?>
				<div class="input-group login_line">
					<span class="input-group-addon"><img src="<?php echo base_url('assets/img/password.png'); ?>" /></span>
					<input tabindex="2" class="form-control" type="password" name="pwd_confirm" value="<?php echo set_value('pwd_confirm'); ?>" placeholder="Retype Password" />
				</div>
				<?php if (form_error('pwd_confirm') != '' ) :?>  
				<?php echo form_error('pwd_confirm'); ?>
				<?php endif; ?>
				
				<br />
				<?php if ($msg = $this->session->flashdata('pwd_reset_token_error')) : ?>
				<div>Wrong random token strings for this page.</div>
				<?php else : ?>
				<input tabindex="3" type="submit" name="pwdresetform" value="RESET PASSWORD" style="border: 0; background-color: #4CB781; color: #9CE4BF; font-weight: bold; padding: 10px 50px; font-size: 11px; " />
				<?php endif; ?>
			</form>
			<?php endif; ?>
		</div>
	</div>
</div>
