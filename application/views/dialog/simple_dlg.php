<!-- Modal -->

<div class="modal fade" id="simple_notification_action" tabindex="-1" role="dialog" aria-labelledby="simple_notification_actionLabel" aria-hidden="true" style="overflow: auto;">
  <div class="modal-dialog" style="width: 30em; max-width: 90%; margin: 10% auto 0; text-align: center;">
    <div class="modal-content" style="background-color: #EBEFF2;">
      <div class="modal-header" style="border: none; padding-bottom: 0; position: relative;" >
        <button type="button" data-dismiss="modal" aria-hidden="true" class="close" style="position: absolute;  background-color: #7A7A7A; border-radius: 50%; right: -1.2em; top: -1em; opacity: 0.8;">
        	<img src="<?php echo base_url('assets/img/dialog_close.png'); ?>" />
        </button>
 	    <h4 class="modal-title" id="simple_notification_actionLabel" style="font-weight: bold;"></h4>
      </div>
      <div class="modal-body" style=" padding: 10px 20px;">
			<p id="simple_notification_message" style="text-align: center;" ></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<style>
#simple_select_action button.btn {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 10px;
  padding: 3px 0;
  width: 70px;
}
#simple_select_action #simple_select_message { margin: 10px 0 30px; }
#simple_select_action #simple_select_button_panel {margin: 10px 0; }

</style>
<div class="modal fade" id="simple_select_action" tabindex="-1" role="dialog" aria-labelledby="simple_select_actionLabel" aria-hidden="true" style="overflow: auto;">
  <div class="modal-dialog" style="width: 40em; max-width: 90%; margin: 10% auto;">
    <div class="modal-content" style="background-color: #EBEFF2;">
      <div class="modal-header" style="border: none; padding-bottom: 0; position: relative;" >
        <button type="button" data-dismiss="modal" aria-hidden="true" class="close" style="position: absolute; background-color: #7A7A7A; border-radius: 50%; right: -1.2em; top: -1em; opacity: 0.8;">
        	<img src="<?php echo base_url('assets/img/dialog_close.png'); ?>" />
        </button>
 	       	<h4 class="modal-title" id="simple_select_actionLabel" style="font-weight: bold;"></h4>
      </div>
      <div class="modal-body" style=" padding: 10px 20px;">
			<p id="simple_select_message" style="text-align: center;" ></p>
			<div style="text-align: center;" id="simple_select_button_panel" ></div>
      </div>
    </div>
  </div>
</div>
