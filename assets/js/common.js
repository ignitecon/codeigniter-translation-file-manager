var simple_select_callback;
var simple_notification_callback;

$(document).ready(function()
{
	$("#simple_select_action").on('hidden.bs.modal', function (e) {
	//	var target = e.target;
		simple_select_callback(e);
	});

	$("#simple_notification_action").on('hidden.bs.modal', function (e) {
		//	var target = e.target;
		simple_notification_callback(e);
	});
});

function open_simple_dialog(option)
{
	var dialog;
	
	if(option.type == 'notification')
	{
		dialog = $("#simple_notification_action");
		dialog.find(".modal-title").html(option.title);
		dialog.find("#simple_notification_message").html(option.message);

		simple_notification_callback = function (e) 
		{
			if(option.callback)
			{
				option.callback(e);
			}
		};
	}
	else // select
	{
		dialog = $("#simple_select_action");
		dialog.find(".modal-title").html(option.title);
		dialog.find("#simple_select_message").html(option.message);
		dialog.find("#simple_select_button_panel button").remove();
		
		$.each(option.buttons, function(index, button_label)
		{
			var button = $('<button type="button" class="btn btn-default" data-dismiss="modal" style="margin: 0 10px;"></button>');
			button.html(button_label);
			dialog.find("#simple_select_button_panel").append(button);

			button.click(function(e){
				var obj = $(this);
				dialog.attr('selected_action', obj.html());
			});
		});
		
		simple_select_callback = function (e) 
		{
//			var target = e.target;
			if(option.callback)
			{
				var action = dialog.attr('selected_action');
//				option.callback(dialog.attr('selected_action'));
				option.callback(action);
			}
		};
	}
	
	dialog.modal('show');
}
