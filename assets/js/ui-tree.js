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
                	var selectedItemName = selectedItem.find(".tree-item-name").attr('url');
                	$(treePanel).parent().parent().find('.selected_lang_file').html(selectedItemName);
                	
                    // get content of the selected language file
                    $.ajax({
                        type: "post",
                        cache: false,
                        url: site_url + "transhome/getFileInfo",
                        dataType: "json",
                        data: { path : selectedItemName },
                        success: function (res) 
                        {
                        	$(treePanel).parent().parent().parent().find(".lang_file_content textarea").val(res.content);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            //alert(thrownError);
                        },
                        async: false
                    });
                	
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