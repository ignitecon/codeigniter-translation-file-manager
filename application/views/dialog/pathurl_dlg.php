<!-- Modal -->
<style>
	#pathURLModal .input-group { padding: 2px; }
	#pathURLModal .input-group .form-control { min-height: 34px; height: auto; }
	#pathURLModal .input-group .input-group-addon { max-width: 100px; min-width: 100px; font-size: 12px;}
	#pathURLModal input.form-control { font-size: 12px;}
	#pathURLModal ul.dropdown-menu { max-height: 200px; min-width: 50px; overflow: auto; }
	#pathURLModal ul.dropdown-menu li a { font-size: 12px; }

#pathURLModal #page_urls > div {
  background-color: #ece9d8;
  font-size: 11px;
  margin: 0 0 2px 2px;
  padding: 2px 6px 2px 10px;
}
#pathURLModal #page_urls > div:before {
	content: "<?php echo $url_root.'/'; ?>";
}	
#pathURLModal #page_urls > div span {
  background-color: #dddbd0;
  border-radius: 6px;
  cursor: pointer;
  float: right;
  margin-left: 4px;
  padding: 0 5px;
}
#pathURLModal #alert_panel .alert {
  font-family: Lato Regular;
  font-size: 12px;
  margin: 0 22px;
  padding: 5px 17px;
}
</style>
<div class="modal fade" id="pathURLModal" tabindex="-1" role="dialog" aria-labelledby="pathURLLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="pathURLModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="input-group ">
			        <span class="input-group-addon">Path</span>
					<div type="text" id="path" class="form-control"></div>
		        </div>
				<div class="input-group ">
			        <span class="input-group-addon">Page URL Links</span>
					<div type="text" id="page_urls" class="form-control" ></div>
		        </div>
				<div class="input-group">
			        <span class="input-group-addon">&nbsp;</span>
			        <div class="input-group-addon" style="max-width: inherit; padding-right: 0px; border-right: medium none; background-color: transparent;"><?php echo $url_root.'/'; ?></div>
			        <input class="form-control" id="page_url_to_add" name="page_url_to_add" style="border-left: medium none; padding-left: 0px;" />
			        <span class="input-group-addon"  style="padding: 0;">
						<button id="path_page_url_add_btn" class="btn" type="button" style="width: 100%; padding: 6px 0; font-size: 12px;">Add</button>
			        </span>
		        </div>
			</div>
			<div style="" id="alert_panel"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="path_page_url_action_exe">OK</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function()
{
	var tr_in_edit;
	
	$('#pathURLModal').on('show.bs.modal', function (ev) 
	{
		$(this).find("#alert_panel").empty();
		
		var btn = ev.relatedTarget;
		$(this).find(".modal-title").html('Edit Link to Page URLs');
		var parents = $(btn).parents('tr');
		tr_in_edit = parents.first();
		$(this).find("#path").html($(tr_in_edit).find('td:eq(1)').html());
		$(this).find("#page_url_to_add").val("");
		$(this).find("#page_urls").empty();

		$.each($(tr_in_edit).find('td:eq(2) > div'), function(index, _div_)
		{
			var div = $("<div>" + $(_div_).html() + "<span>&times;</span></div>");
			$("#pathURLModal #page_urls").append(div);
			div.find('span').click(function(){$(this).parent().remove();});
		});
	});

	$("#pathURLModal #path_page_url_add_btn").click(function()
	{
		var page_url_to_add = $("#pathURLModal #page_url_to_add").val();
		if(page_url_to_add.trim() == "") return;
			
		var included = false;
		$.each($("#pathURLModal #page_urls > div"), function(index, _div_)
		{
			var div = $(_div_).clone();
			$(div).find('span').remove();
			if($(div).html() == page_url_to_add) { included = true; return false; }
		});

		if(included)
		{ 
        	var alert = $('<div class="alert alert-danger" ><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button></div>');
        	$("#pathURLModal #alert_panel").empty().append(alert);
			alert.append("Duplicated page URLs are not allowed.");
			return;
		}
		else
		{
			$("#pathURLModal #alert_panel").empty();
			var div = $("<div>" + page_url_to_add + "<span>&times;</span></div>");
			$("#pathURLModal #page_urls").append(div);
			div.find('span').click(function(){$(this).parent().remove();});
		}
	});
	
	$("#pathURLModal #path_page_url_action_exe").click(function()
	{
		$("#pathURLModal").modal('hide');

		var tr = $(tr_in_edit);
		$(tr).find('td:eq(2)').empty();

		$.each($("#pathURLModal #page_urls > div"), function(index, div)
		{
			$(div).find('span').remove();
			$(tr).find('td:eq(2)').append($("<div class='path_url' >" + $(div).html() + "</div>"));
		});
	});
});
</script>