/*
 * Fuel UX Spinner
 * https://github.com/ExactTarget/fuelux
 * Improved by keenthemes for metronic theme
 * Copyright (c) 2012 ExactTarget
 * Licensed under the MIT license.
 */

!function ($) {
	var Tree = function (element, options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.tree.defaults, options);

		this.$element.on('click', '.tree-item', $.proxy( function(ev) { this.selectItem(ev); } ,this));
		this.$element.on('click', '.tree-folder-header .fa', $.proxy( function(ev) { this.selectFolder(ev); }, this));
		this.$element.on('click', '.tree-folder-header .tree-folder-check', $.proxy( function(ev) { this.selectFolderCheck(ev); }, this));

		this.render();
	};

	Tree.prototype = {
		constructor: Tree,

		render: function () {
			this.populate(this.$element);
		},

		populate: function ($el) {
			var self = this;
			var $parent = $el.parent();
//			var loader = $parent.find('.tree-loader:eq(0)');
			var $name = $el.find('.tree-folder-name').html();			
//			loader.show();			
			this.options.dataSource.data($el.data(), function (items) {
//				loader.hide();				
				$.each( items.data, function(index, value) {
					var $entity;

					if(value.type === "folder") {						
						$entity = self.$element.find('.tree-folder:eq(0)').clone().show();
						$entity.find('.tree-folder-name').html(value.name);
						$entity.find('.tree-folder-name').attr('url',value.url);
//						$entity.find('.tree-loader').html(self.options.loadingHTML);
						$entity.find('.tree-folder-header').data(value);
					} else if (value.type === "item") {
						$entity = self.$element.find('.tree-item:eq(0)').clone().show();
						$entity.find('.tree-item-name').html(value.name);
						$entity.find('.tree-item-name').attr('url',value.url);
						$entity.data(value);
					} else if (value.type ==="unknown"){
						$entity = self.$element.find('.tree-item:eq(0)').clone().show();
						$entity.find('.tree-item-name').attr('class','unknown').css('color','red').css('background-color','#4f1f2f').html(value.name);
						$entity.find('.tree-item-name').attr('url',value.url);
						$entity.data(value);
					}				

					var dataAttributes = value.dataAttributes || [];
					$.each(dataAttributes, function(key, value) {
						switch (key) {
						case 'class':
						case 'classes':
						case 'className':
							$entity.addClass(value);
							break;

						// id, style, data-*
						default:
							$entity.attr(key, value);
							break;
						}
					});

					if($el.hasClass('tree-folder-header')) {
						$parent.find('.tree-folder-content:eq(0)').append($entity);
					} else {
						$el.append($entity);
					}
				});

				// return newly populated folder
				self.$element.trigger('loaded', $parent);
			});
		},
		populate1: function ($el) {
			var self = this;			
			var $parent = $el.parent();
//			var loader = $parent.find('.tree-loader:eq(0)');
			var $name = $el.find('.tree-folder-name').html();
			var url = $el.find('.tree-folder-name').attr('url');			
//			loader.show();
            $.ajax({
                type: "post",
                cache: false,
                url: site_url + "transhome/getFolderInfo",
                dataType: "json",
                data: {path : url},
                success: function (res) {
//                	loader.hide();	
    				$.each( res, function(index, value) {
    					var $entity;
    					if(value.type == "folder") {						
    						$entity = self.$element.find('.tree-folder:eq(0)').clone().show();
    						$entity.find('.tree-folder-name').html(value.name);
    						$entity.find('.tree-folder-name').attr('url',value.url);
//    						$entity.find('.tree-loader').html(self.options.loadingHTML);
    						$entity.find('.tree-folder-header').data(value);
    					}
    					else if (value.type == "item") 
    					{
    						$entity = self.$element.find('.tree-item:eq(0)').clone().show();
    						$entity.find('.tree-item-name').html(value.name);
    						$entity.find('.tree-item-name').attr('url',value.url);
    						$entity.data(value);
    					}
    					else if (value.type == "unknown")
    					{
    						$entity = self.$element.find('.tree-item:eq(0)').clone().show();
    						$entity.find('.tree-item-name').attr('class','unknown').css('color','red').css('background-color','#4f1f2f').html(value.name);
    						$entity.find('.tree-item-name').attr('url',value.url);
    						$entity.data(value);
    					}
    					else if (value.type == "more")
    					{
    						$entity = self.$element.find('.tree-item:eq(0)').clone().show();
    						$entity.find('.tree-item-name').attr('class','more').css('color','green').css('background-color','blue').html(value.name);
    						$entity.find('.tree-item-name').attr('url',value.url);
    						$entity.data(value);
    					}
    					

    					var dataAttributes = value.dataAttributes || [];
    					$.each(dataAttributes, function(key, value) {
    						switch (key) {
    						case 'class':
    						case 'classes':
    						case 'className':
    							$entity.addClass(value);
    							break;

    						// id, style, data-*
    						default:
    							$entity.attr(key, value);
    							break;
    						}
    					});

    					if($el.hasClass('tree-folder-header')) {
    						$parent.find('.tree-folder-content:eq(0)').append($entity);
    					} else {
    						$el.append($entity);
    					}
    				});

    				// return newly populated folder
    				self.$element.trigger('loaded', $parent);
                },
                error: function (xhr, ajaxOptions, thrownError) {

                },
                async: false
            });
				

		},
		selectItem: function (ev) 
		{
			var el = ev.currentTarget;
//			ev.stopImmediatePropagation();
			var $el = $(el);
			this.options.dataSource.displaySelectedItemName($el);
			
			$('#folder_name').val($el.find('.tree-item-name').attr('url'));			
			var $all = this.$element.find('.tree-selected');
			var data = [];

			if (this.options.multiSelect) {
				$.each($all, function(index, value) {
					var $val = $(value);
					if($val[0] !== $el[0]) {
						data.push( $(value).data() );
					}
				});				
			} else if ($all[0] !== $el[0]) {				
				$all.removeClass('tree-selected').find('i');
				data.push($el.data());
			}

			if (this.options.selectable) 
			{
				var eventType = 'selected';
				if($el.hasClass('tree-selected')) {
					eventType = 'unselected';
					$el.removeClass('tree-selected');
//					$el.find('i').removeClass('fa fa-check').addClass('tree-dot');
				} else {
					$el.addClass ('tree-selected');
//					$el.find('i').removeClass('tree-dot').addClass('fa fa-check');
					if (this.options.multiSelect) {
						data.push( $el.data() );
					}
				}
			}

			if(data.length) {
				this.$element.trigger('selected', {info: data});
			}

			// Return new list of selected items, the item
			// clicked, and the type of event:
			$el.trigger('updated', {
				info: data,
				item: $el,
				eventType: eventType
			});
		},

		selectFolder: function (ev) 
		{
			var el = $(ev.currentTarget).parent();
			ev.stopImmediatePropagation()
			var $el = $(el);			
			var $parent = $el.parent();
			var $treeFolderContent = $parent.find('.tree-folder-content');			
			var $treeFolderContentFirstChild = $treeFolderContent.eq(0);			
			var eventType, classToTarget, classToAdd;
			
			var $all = this.$element.find('.tree-selected');
			var data = [];

			if (this.options.multiSelect) {
			} else if ($all[0] !== $el[0]) {				
				$all.removeClass('tree-selected');				
			}
			
			$el.addClass('tree-selected');
			
			if($el.data().top == 'yes')
			{
				$('#folder_name').val("top");
			}
			else
			{
				$('#folder_name').val($el.find('.tree-folder-name').attr('url'));
			}
			
			if ($el.find('.fa.fa-folder').length) 
			{
				eventType = 'opened';
				classToTarget = '.fa.fa-folder';
				classToAdd = 'fa fa-folder-open';

				$treeFolderContentFirstChild.show();
				if (!$treeFolderContent.children().length) {
					this.populate1($el);
				}
			} 
			else 
			{
				eventType = 'closed';
				classToTarget = '.fa.fa-folder-open';
				classToAdd = 'fa fa-folder';

				$treeFolderContentFirstChild.hide();
				if (!this.options.cacheItems) {
					$treeFolderContentFirstChild.empty();
				}
			}

			$parent.find(classToTarget).eq(0)
				.removeClass('fa fa-folder fa-folder-open')
				.addClass(classToAdd);

			var chVal = $el.find('> .tree-folder-check')[0].checked;
			if(chVal)
			{
				$.each($el.parent().find(".tree-folder-check"), function(index, ch){ ch.checked = chVal; });
			}

			this.$element.trigger(eventType, $el.data());
		},

		selectFolderCheck: function (ev) 
		{
			var check = $(ev.currentTarget);
			var el = $(ev.currentTarget).parent();
			
			var chVal = check[0].checked;
			
			// make the check status for lower checkboxes same as changed checkbox
			$.each($(check).parent().parent().find(".tree-folder-check"), function(index, ch){ ch.checked = chVal; });
		
			// check the check status for higher checkboxes and reflect the status of changed checkbox
			if(!chVal)
			{
				var parents = $(check).parents('.tree-folder');
				$.each(parents, function(index, parent){
					if(index == 0) return true;
					$(parent).find('> .tree-folder-header > .tree-folder-check')[0].checked = chVal;
				});
			}
			else
			{
				var parents = $(check).parents('.tree-folder');
				$.each(parents, function(index, parent){
					if(index == 0) return true;
					var childChecks = $(parent).find('> .tree-folder-content > .tree-folder > .tree-folder-header > .tree-folder-check');
					var foundNotChecked = false;
					
					$.each(childChecks, function(index, childCheck)
					{
						if(!childCheck.checked) foundNotChecked = true;
					});
					
					if(foundNotChecked) return false;
					
					$(parent).find('> .tree-folder-header > .tree-folder-check')[0].checked = true;
				});
			}
			
			this.options.dataSource.selectCheckBoxCallback();
		},
		
		selectedItems: function () {
			var $sel = this.$element.find('.tree-selected');
			var data = [];

			$.each($sel, function (index, value) {
				data.push($(value).data());
			});
			return data;
		},

		// collapses open folders
		collapse: function () {
			var cacheItems = this.options.cacheItems;

			// find open folders
			this.$element.find('.fa.fa-folder-open').each(function () {
				// update icon class
				var $this = $(this)
					.removeClass('fa fa-folder fa-folder-open')
					.addClass('fa fa-folder');

				// "close" or empty folder contents
				var $parent = $this.parent().parent();
				var $folder = $parent.children('.tree-folder-content');

				$folder.hide();
				if (!cacheItems) {
					$folder.empty();
				}
			});
		}
	};


	// TREE PLUGIN DEFINITION

	$.fn.tree = function (option, value) {
		var treePanel = $(this);
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('tree');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('tree', (data = new Tree(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};

	$.fn.tree.defaults = {
		selectable: true,
		multiSelect: false,
		loadingHTML: '<div>Loading...</div>',
		cacheItems: true
	};

	$.fn.tree.Constructor = Tree;
}(window.jQuery);