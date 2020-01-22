var UITree = function () 
{
    return {
        //main function to initiate the module
        init: function ( treePanel ) 
        {        	
            var DataSourceTree = function (options) {
                this._data  = options.data;
                this._delay = options.delay;
            };

            DataSourceTree.prototype = 
            {
                data: function (options, callback) {
                    var self = this;

                    setTimeout(function () 
                    {
                        var data = $.extend(true, [], self._data);
                        callback({ data: data });
                    }, this._delay)
                },
                displaySelectedItemName : function(selectedItem)
                {
                },
                selectCheckBoxCallback : function()
                {
                	var lang_files = "";
                	$.each($(".lang_files_browse .tree-folder-check"), function(index, check){
                		if(!check.checked) return true;
                		var path = $(check).prev().attr('url');
                		lang_files += (path + '|');
                	});
                	
                	if(lang_files.length > 0) lang_files = lang_files.slice(0, -1); // delete last character ( '|' )
                	var lang_files_input = $("#lang_setting_form").find("input[name='language_files_" + lang_cur_selected + "']");
                	$(lang_files_input).val(lang_files);
                }
            };                        

            // get the folder structure of language root folder
            $.ajax({
                type: "post",
                cache: false,
                url: site_url + "transhome/getFolderInfo",
                dataType: "json",
                data: { path : '' },
                success: function (res) {                 	
                	var treeDataSource = new DataSourceTree({data: res,delay: 0});
                	$(treePanel).tree({
                        dataSource: treeDataSource,
                        loadingHTML: '<img src="assets/img/input-spinner.gif"/>',
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //alert(thrownError);
                },
                async: false
            });
        }

    };

}();