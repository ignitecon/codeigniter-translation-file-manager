<style>
#translate_page .compare_table { margin: 0; border-left: 1px solid black; border-right: 1px solid black; }
#translate_page .table1 { border-left: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD; }
#translate_page .compare_table tr th, #translate_page .compare_table tr td { word-break: break-all; padding: 8px 5px 8px 20px !important; }
#translate_page .compare_table tr th { background-color: black; border: medium none !important; color: white; padding: 13px 20px !important; }
#translate_page .compare_table tr td { border-top: none; border-bottom: 1px solid #DDDDDD; }
#translate_page .compare_table tr td input { width: 100%; color: #2C9CDD; border: 1px solid transparent; font-size: 14px; background-color: transparent; }
#translate_page .compare_table tr td input:focus { border: 1px solid #dddddd; }
#translate_page .compare_table tr.empty { background-color: #F2DEDE; }
#translate_page .compare_table tr.empty input { color: black; font-size: 12px; }
#translate_page .compare_table tr.deled td:nth-child(2) {
  background-image: url("<?php echo base_url('assets/img/error.png'); ?>");
  background-position: 97% center;
  background-repeat: no-repeat;
  color: #cd2f34;
  font-size: 13px;
  vertical-align: middle;
}
#translate_page .compare_table tr.added td:nth-child(2) { background-image: url("<?php echo base_url('assets/img/warning.png'); ?>"); background-position: 97% center; background-repeat: no-repeat; }
#translate_page #assoc_pages span { color: blue; cursor: pointer; }
#translate_page #assoc_pages span:before {content: "/";}
#translate_page #assoc_pages span:after {content: ", ";}
#translate_page #assoc_pages span:last-child:after {content: "";}
</style>
<?php 
	$current_revision = $this->session->userdata('current_revision');
	
	$langMap = array();
	foreach($languages as $language)
	{
		$langMap[$language['id']] = $language['name'];
		if($language['id'] == $current_revision['master_lang_id']) $master_lang_name = $language['name'];
	}
?>
<div class="container-fluid" id="translate_page" style="background-color: #F5F5F5; height: 100%;">
	<div class="row" style="height: 100%;">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="height: 100%;">
			<div style="padding: 20px 0 10px; font-size: 22px;  font-weight: bold; font-family: Bebas Neue;">
				<span id="selected_role_disp">TRANSLATE</span> <span class="selected_lang_file_disp" style="color: #59AE84; text-transform: uppercase;"></span>
			</div>
			<div class="table-responsive" style="font-weight: bold; font-size: 12px; height: 77%; overflow: auto; color: gray;">
				<table class="compare_table table table1" >
			  		<thead>
			  			<tr>
			  				<th width="25%">Key</th>
			  				<th><font id="slave_lang_name" >&lt;Slave Language&gt;</font> / <?php echo $master_lang_name; ?></th>
			  			</tr>
			  		</thead>
			  		<tbody>
			  		</tbody>
				</table>
			</div>
			<div style="padding: 20px 0; display: none; text-align: right;" id="translate_button_panel" >
				<button onclick="save_slave_lang_translation();" style="background-color:#4CB781; color: white; border: none; padding: 7px 15px;"><b>Save</b></button>
				<button onclick="markascomplete_slave_lang_translation();" style="background-color:#4CB781; color: white; border: none;padding: 7px 15px;"><b>Save &amp; Mark As Complete</b></button>
			</div>
			<div style="padding: 20px 0; display: none; text-align: right;" id="translate_mark_as_result_panel" >
				<button style="background-color: white; color: #4CB781; border: none; padding: 7px 15px;"><b>Marked As Completed</b></button>
				<button onclick="unmarkascomplete_slave_lang_translation();" style="background-color:#4CB781; color: white; border: none;padding: 7px 15px;"><b>Unmark As Complete</b></button>
			</div>
		</div>
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="height: 100%;">
			<div style="padding: 19px 0 15px;">
				<div style="line-height: 1em; font-weight: bold; font-size: 11px;">Associated page(s) for : <span class="selected_lang_file_disp" style="text-transform: uppercase;"></span></div>
				<div style="color: #4CB781; font-weight: bold; font-size: 11px;"><?php echo $current_revision['target_ci_url']; ?><span id="assoc_pages"></span></div>					
			</div>
			<div style="overflow: auto; height: 84%;" >
				<iframe id="target_site" src="<?php echo $current_revision['target_ci_url']; ?>" width="100%" height="100%"></iframe>
			</div>
		</div>
	</div>
</div>
<script>

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

/*
var repo_test_map = {};
<?php foreach ($statistics as $statis) : ?>
repo_test_map['<?php echo $statis['language_id']; ?>'] = {};
<?php foreach ($statis['status'] as $entry) : ?>
repo_test_map['<?php echo $statis['language_id']; ?>']['<?php echo $entry['path']; ?>'] = <?php echo json_encode($entry); ?>;
<?php endforeach; ?>
<?php endforeach; ?>

repo_status_map = repo_test_map;
*/

var selected_role;
var selected_slave_lang;
var temp_selected_slave_lang;
var selected_lang_file;
var lang_list = {};

<?php foreach (array('translator', 'proofer') as $role) : ?>
<?php foreach ($current_revision['users'] as $user) : ?>
<?php if( 	$this->session->userdata('is_global_admin') == 'yes' || 
			$user['moderator_id'] == $this->session->userdata('id') || 
			$user[$role.'_id'] == $this->session->userdata('id')) : ?>
<?php if($role == 'proofer' && $user['proofer_id'] == '0') continue; ?>
if(!lang_list['<?php echo $role; ?>']) lang_list['<?php echo $role; ?>'] = [];
lang_list['<?php echo $role; ?>'].push({id: '<?php echo $user['language_id']; ?>', name: '<?php echo $langMap[$user['language_id']]; ?>'});
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

var langMap = <?php echo json_encode($langMap); ?>;

var path_page_urls = <?php echo json_encode($path_page_urls); ?>;
var clean_status_map = <?php echo json_encode($clean_status); ?>;

function loadTwoLangFilesMap()
{
	if(!selected_role) return;
	if(!selected_slave_lang) return;
	if(!selected_lang_file) return;

	$.ajax
	({
		type: "post",
		cache: false,
		url: site_url + "transhome/getTwoLangFilesMap",
		dataType: "json",
		data: { slave_lang : selected_slave_lang, lang_file : selected_lang_file, stage : selected_role },
		success: function (res) 
		{
			var tbl = $(".table1 tbody");
			tbl.empty();

			$("#translate_button_panel").hide();
			$("#translate_mark_as_result_panel").hide();

			if(res.result.status.status == "Translation Not Completed")
			{
	            open_simple_dialog
	            ({	
		            type: 'notification', title: 'Language File Loading', message: 
			            '<div class="alert alert-success" >' + 
				            'Sorry, The language file selected is not translated and completed yet. ' + 
							'Please wait for it to be translated and mark it as completed. ' +
							'If you have translator role, please change role to translator at [ACT AS] and try to translate first.' + 
						'</div>'
		        });
				
				return;
			}

			$.each(res.result.merge_repo, function(index, entry)
			{
//				var isempty = entry[2].trim() == '' ? 'empty' : '';
				var cls = "";

				if(clean_status_map[selected_lang_file])
				{
					if($.inArray(entry[0], clean_status_map[selected_lang_file]['added_keys']) >= 0) cls = 'added';
				}


				var tr = $("<tr class='" + cls + "' />");
				tr.append($("<td width='25%'>" + entry[0] + "</td>"));
				tr.append($("<td><input type='text' value='" + entry[2] + "' placeholder='*** Missing translation! ***' /><br /><i>" + entry[1] + "</i></td>"));
				tbl.append(tr);
			});

			for(var ii = 0; ii < clean_status_map[selected_lang_file]['deled_keys'].length; ii++)
			{
				var tr = $("<tr class='deled' />");
				tr.append($("<td width='25%'>" + clean_status_map[selected_lang_file]['deled_keys'][ii] + "</td>"));
				tr.append($("<td><i>Deleted Key</i></td>"));
				tbl.append(tr);
			} 

			$("#slave_lang_name").html(langMap[selected_slave_lang]);
			
			switchBasedOnStatus(res.result.status);
		},
		error: function (xhr, ajaxOptions, thrownError) 
		{
			alert(thrownError);
		},
		async: false
	});

	var pages = path_page_urls[selected_lang_file];
	if(pages)
	{
		for(var ii = 0; ii < pages.length; ii++)
		{
			$("#translate_page #assoc_pages").append('<span>' + pages[ii] + '</span>');
		}
	
		$("#translate_page #assoc_pages span:first").click();
	}
}

function save_slave_lang_translation(complete)
{
	// TO DO : 1. Process for the key including special character, ex : © = &copy;
	if(!complete) complete = false;
	
	var lang_arr = [];

	$.each($(".table1 tbody tr"), function(index, tr)
	{
		if($(tr).hasClass('deled')) return true;
		var key = $(tr).find('td:eq(0)').html();
		var val = $(tr).find('td:eq(1) input').val();
		lang_arr.push([key, val]);
	});

	$.ajax
	({
        type: "post",
        cache: false,
        url: site_url + "transhome/saveSlaveLangFileMap",
        dataType: "json",
        data: { slave_lang : selected_slave_lang, lang_file : selected_lang_file, lang_arr : lang_arr, stage : selected_role, complete: complete },
        success: function (res) 
        {
            if(res.result == 'success')
            {
            	switchBasedOnStatus(res.status);

            	if(selected_role == 'translator')
            	{
            		$('#translateModal .modal-title').html('TRANSLATION SAVE RESULT');
            		$('#translateModal table.basic_table tbody tr:eq(0) td:eq(1)').html(res.status.words);
            		$('#translateModal table.basic_table tbody tr:eq(0) td:eq(2)').html("-");
            		$('#translateModal table.basic_table tbody tr:eq(1) td:eq(1)').html(res.status.keys);
            		$('#translateModal table.basic_table tbody tr:eq(1) td:eq(2)').html("-");
            		$('#translateModal table.basic_table tbody tr:eq(2) td:eq(1)').html(res.status.total_empty);
            		$('#translateModal table.basic_table tbody tr:eq(3) td:eq(1)').html(res.status.total_keys);
            	}
            	else
            	{
            		$('#translateModal .modal-title').html('READ PROOF SAVE RESULT');
            		$('#translateModal table.basic_table tbody tr:eq(0) td:eq(1)').html(res.status.words);
            		$('#translateModal table.basic_table tbody tr:eq(0) td:eq(2)').html(res.status.translate_words);
            		$('#translateModal table.basic_table tbody tr:eq(1) td:eq(1)').html(res.status.keys);
            		$('#translateModal table.basic_table tbody tr:eq(1) td:eq(2)').html(res.status.translate_keys);
            		$('#translateModal table.basic_table tbody tr:eq(2) td:eq(1)').html(res.status.total_empty);
            		$('#translateModal table.basic_table tbody tr:eq(3) td:eq(1)').html(res.status.total_keys);
            	}
            	
            	$('#translateModal').modal('show');
				            	
	        	var lang_file = selected_lang_file.replace(/(\/|\\)/, '');
	        	var info = repo_status_map[selected_slave_lang][lang_file];
	        	if(!info)
	        	{
	        		repo_status_map[selected_slave_lang][lang_file] = {};
	        	}

	        	repo_status_map[selected_slave_lang][lang_file][selected_role + '_status'] = complete ? 'Completed' : 'In Progress';

	        	$("#translate_page #target_site")[0].contentWindow.location.reload();
            }
		},
        error: function (xhr, ajaxOptions, thrownError) 
        {
            alert('Error Occured while saving lang information.');
        },
        async: false
    });
}

function markascomplete_slave_lang_translation()
{
	save_slave_lang_translation(true);
}

function unmarkascomplete_slave_lang_translation()
{
	$.ajax
	({
        type: "post",
        cache: false,
        url: site_url + "transhome/unmarkAsCompleted",
        dataType: "json",
        data: { slave_lang : selected_slave_lang, lang_file : selected_lang_file, stage : selected_role },
        success: function (res) 
        {
            if(res.result == 'success')
            {
            	switchBasedOnStatus(res);

	        	var lang_file = selected_lang_file.replace(/(\/|\\)/, '');
	        	var info = repo_status_map[selected_slave_lang][lang_file];
	        	if(!info)  repo_status_map[selected_slave_lang][lang_file] = {}; 

	        	repo_status_map[selected_slave_lang][lang_file][selected_role + '_status'] = 'In Progress';
            }
		},
        error: function (xhr, ajaxOptions, thrownError) 
        {
            alert('Error Occured while unmarking language file.');
        },
        async: false
    });
}

function switchBasedOnStatus(status)
{
	if(status.status != 'In Progress')
	{
		 $("#translate_button_panel").hide();
		 $("#translate_mark_as_result_panel").show();
	}
	else
	{
		 $("#translate_button_panel").show();
		 $("#translate_mark_as_result_panel").hide();
	}
}

jQuery(document).ready(function() 
{
	$("li.actas ul.dropdown-menu li").click(function()
	{
		var rolename = $(this).html();
		$("#actas").html(rolename);

		selected_role = $(this).attr('role');
		$("#selected_role_disp").html(rolename);

		if(selected_role == 'translator') $("#translate_status").html('TRANSLATING');
		if(selected_role == 'proofer') $("#translate_status").html('PROOF READING');

//		selected_slave_lang = undefined; // This is in order to prevent loading language file automatically

		var language_list = $("li.menu-dropdown ul.dropdown-menu #language_list");
		language_list.empty();

		$.each(lang_list[selected_role], function(index, lang)
		{
			language_list.append($("<li lang_id='" + lang.id + "'>" + lang.name + "</li>"))	
		});

		language_list.find('li').mouseenter(function(ev)
		{
			language_list.find('li').removeClass('selected');
			$(this).addClass('selected');

			temp_selected_slave_lang = $(this).attr('lang_id');
//			$("#slave_lang_name").html(selected_slave_lang);

			var lang_file_list = $(".menu-dropdown ul.dropdown-menu .lang-file-list .block span");
			$.each(lang_file_list, function(index, span)
			{
				$(span).removeClass('unchanged').removeClass('uneditable').removeClass('inprogress').removeClass('completed');
				var file_name = $(span).html().replace(/(\/|\\)/, '');
				if(!repo_status_map[temp_selected_slave_lang][file_name])
				{
					if(selected_role == 'translator') $(span).addClass('unchanged');
					else if(selected_role == 'proofer') $(span).addClass('uneditable');
					else {}
				}
				else
				{
					var status = repo_status_map[temp_selected_slave_lang][file_name][selected_role + '_status'];
					if(status == 'In Progress') $(span).addClass('inprogress');
					else if(status == 'Completed') $(span).addClass('completed');
					else if(selected_role == 'translator') $(span).addClass('unchanged');
					else 
					{
						var translator_status = repo_status_map[temp_selected_slave_lang][file_name]['translator_status'];	
						if(translator_status != 'Completed') $(span).addClass('uneditable');
						else $(span).addClass('unchanged');
					}	
				}	
			});
		});

		language_list.find('li:eq(0)').mouseenter();
		
		loadTwoLangFilesMap();
	});
	
	$($("li.actas ul.dropdown-menu li")[0]).click();

	var lang_file_list = $(".menu-dropdown ul.dropdown-menu .lang-file-list .block span");
	lang_file_list.click(function()
	{
		lang_file_list.removeClass('selected');
		$(this).addClass('selected');

		selected_lang_file = $(this).html();
		$(".selected_lang_file_disp").html(selected_lang_file);

		selected_slave_lang = temp_selected_slave_lang;
		
		loadTwoLangFilesMap();
	});

	$('li.translator').on('shown.bs.dropdown', function () 
	{
		if(!selected_slave_lang) return;

		$.each($("div#language_list li"), function(index, lang_li)
		{
			if($(lang_li).html() != selected_slave_lang) {}
			else
			{
				$(lang_li).mouseenter();
			}
		});	
	});

	$("#translate_page #assoc_pages").on('click', 'span', function()
	{
		var url = $(this).html();
		$("#translate_page #target_site").attr('src', "<?php echo $current_revision['target_ci_url']; ?>/" + url);
	});

	$("#translate_page").height($('body').height() - 70);
	
	if($.cookie('selected_lang_id') && $.cookie('lang_file'))
	{
		selected_slave_lang = $.cookie('selected_lang_id');
		selected_lang_file = $.cookie('selected_lang_file');
		
		$.cookie('selected_lang_id', null);
		$.cookie('selected_lang_file', null);
		
		loadTwoLangFilesMap();
	}

	
});
</script> 