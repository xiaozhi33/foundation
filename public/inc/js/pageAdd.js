function BrowseServer(functionData)
{
	var finder = new CKFinder();
	finder.basePath = '/inc/ckfinder/';
	finder.startupPath = 'Images:/';
	finder.selectActionData = functionData;
	finder.selectActionFunction = function(fileUrl,data) {
		document.getElementById( data["selectActionData"] ).value = fileUrl;
	}
	finder.popup();
}
function BrowseServer2(functionData)
{
	var finder = new CKFinder();
	finder.basePath = '/inc/ckfinder/';
	finder.startupPath = 'Images:/';
	finder.selectActionData = functionData;
	finder.selectActionFunction = function(fileUrl,data) {
		data["selectActionData"].val(fileUrl);
	}
	finder.popup();
}
//设置模块排序功能
function resetSort() {
	if(framesDom != null) {
		framesDom.sortable( "destroy" );
	}
	framesDom = $("#content .frame").sortable({
		connectWith: ".frame",
		placeholder: "ui-state-module-highlight",
		opacity: 0.7,
		cancel: '.message',
		scrollSpeed: 5,
		stop: function() {
			$('#content .frame').each(function(){
				if($(this).children('.module').size() == 0) {
					$(this).children('.message').show();
				}else {
					$(this).children('.message').hide();
				}
			});
		},
		over: function() {
			$('#content .frame').each(function(){
				if(($(this).children('.ui-state-module-highlight').size() == 0 && $(this).children('.module').size() == 0) || ($(this).children('.ui-sortable-helper').size() == 1 && $(this).children('.ui-state-module-highlight').size() == 0 && $(this).children('.module').size() == 1)) {
					$(this).children('.message').show();
				}else {
					$(this).children('.message').hide();
				}
			});
		},
		out: function() {
			$('#content .frame').each(function(){
				if($(this).children('.ui-state-module-highlight').size() == 0 && $(this).children('.module').size() == 0) {
					$(this).children('.message').show();
				}else {
					$(this).children('.message').hide();
				}
			});
		}
	});
}
//屏幕滚动到某dom显示区域
function scrollScreen(select)
{
	if(jQuery.browser.safari) {
		jQuery('body').animate({scrollTop:jQuery(select).offset().top}, '500');
		return false;
    }
    else {
		jQuery('html').animate({scrollTop:jQuery(select).offset().top}, '500');
		return false;
    }
}
//初始化编辑功能
function init() {
	$('a:not(.splink)').live('click',function(){
		if($(this).attr('href') != '' && $(this).attr('href') != '#') {
			return false;
		}
	});
/**
*
* 页头banner图片替换,切换样式文件及页面seo设置
*
*/
	//banner对话框变量
	banner_control_dia = null;
	//seo对话框变量
	seo_control_dia = null;
	//banner图片设置
	$('<div id="banner_control_dia" style="display:none;" title="页头图片"><form name="" action="" method="POST" enctype="multipart/form-data" target="configIframe"><div class="form"><p>上传图片：<br><input id="banner_img" type="text" value="" size="50" name="image"><a onclick="BrowseServer(\'banner_img\');" href="javascript:void(0);">上传</a></p><p>图片尺寸：<br><input type="text" value="960" size="10" name="width"> × <input type="text" value="340" size="10" name="height"></p><p>图片标题：<br><input type="text" value="" size="50" name="title"></p></div></form></div>').appendTo($('body'));
	$('<div class="change_img"></div>').prependTo($('#header'));
	$('#header .change_img').click(function(){
		if(banner_control_dia == null) {
			banner_control_dia = $('#banner_control_dia').dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				dialogClass:'banner_control_dia dialog',
				width: 400,
				buttons:[{
					text:'提交',
					click:function(){
						$('.mb-header-img img').attr('src',$('#banner_img').val());
						$('.mb-header-img img').attr('alt',$('#banner_control_dia :text[name=title]').val());
						$('.mb-header-img img').attr('title',$('#banner_control_dia :text[name=title]').val());
						var width = parseInt($('#banner_control_dia :text[name=width]').val());
						var height = parseInt($('#banner_control_dia :text[name=height]').val());
						if(width > 0) {
							$('.mb-header-img img').width(width);
						}else {
							$('.mb-header-img img').css('width','auto');
						}
						if(height > 0) {
							$('.mb-header-img img').height(height);
						}else {
							$('.mb-header-img img').css('height','auto');
						}
						$('#banner_img').val('');
						$('#banner_control_dia :text[name=title]').val('');
						banner_control_dia.dialog('close');
					}
				}]
			});
		}
		banner_control_dia.dialog('open');
	});
	
	//SEO设置
	$('<div id="seo_control_dia" style="display:none;" title="SEO设置"><div class="form"><p>关键字：<br><textarea rows="5" name="keywords"></textarea></p><p>页面描述：<br><textarea rows="8" name="description"></textarea></p></div></div>').appendTo($('body'));
	$('<div class="set_icon"></div>').prependTo($('#page_body'));
	$('#page_body>.set_icon').click(function(){
		if(seo_control_dia == null) {
			seo_control_dia = $('#seo_control_dia').dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				dialogClass:'seo_control_dia dialog',
				width: 400,
				buttons:[{
					text:'提交',
					click:function(){
						$('meta[name=keywords]').attr('content',$('#seo_control_dia textarea[name=keywords]').val());
						$('meta[name=description]').attr('content',$('#seo_control_dia textarea[name=description]').val());
						seo_control_dia.dialog('close');
					}
				}]
			});
		}
		seo_control_dia.dialog('open');
	});
	
	//切换样式
	$('#top_control .control_skin li').click(function(){
		$('#skin_css').attr('href',$(this).addClass('active').siblings().removeClass('active').end().attr('skin'));
	});
	
	



/**
*
* 全局变量，预定义参数及dom
*
*/
	
	color_cur_selector = null;
	//参数存储变量
	setting = [];
	//两栏框架栏位调整条
	slider2_bar = $('<div class="slider2 slider"></div>');
	//两栏框架栏位调整条
	slider3_bar = $('<div class="slider3 slider"></div>');
	//模块编辑菜单
	module_menu = $('<div id="module_set" class="ui-widget ui-helper-clearfix"><ul><li class="ui-state-default ui-corner-all"><a class="data ui-icon ui-icon-wrench" href="#">数据</a></li><li class="ui-state-default ui-corner-all"><a class="css ui-icon ui-icon-gear" href="#">样式</a></li><li class="ui-state-default ui-corner-all"><a class="del ui-icon ui-icon-closethick" href="#">删除</a></li></ul></div>').prependTo($('body'));
	//当前调整的slide
	current_slide = null;
	//当前可以排序的模块
	framesDom = null;
	//当前编辑模块
	current_module = null;
	//鼠标划出框架后对slider的延时操作
	destory_slider = null;
	
	
	
	
	
/**
*
* 通用初始化部分
*
*/
	//初始化top_control中的skin 选项
	var skin_url = $('#skin_css').attr('href');
	$('#top_control .control_skin li').filter(function(){
		if($(this).attr('skin') == skin_url) return true;
		else return false;
	}).addClass('active');

	
	//创建返回模块配置数据的iframe
	configIframe = $('<iframe name="configIframe" id="configIframe" style="position:absolute;top:-10000px;left:-10000px;"></iframe>').appendTo('body');
	//鼠标划出framebox框架移除slider
	$('#content .framebox').live('mouseleave',function(){
		destory_slider = setTimeout(function(){
			if(current_slide != undefined) {
				current_slide.slider( "destroy" );
			}
			if($(this).hasClass('framebox2')) {
				slider2_bar.remove();
			}else if($(this).hasClass('framebox3')) {
				slider3_bar.remove();
			}
			destory_slider = null;
		},300);
	});
	$('#content .framebox').live('mouseenter',function(){
		if($(this).find('.slider').size() > 0) {
			clearTimeout(destory_slider);
		}
	});
	//鼠标进入module显示配置按钮
	$('#content .module').live('mouseenter',function(){
		if(current_module != null) {
			current_module.removeClass('curr_module');
		}
		current_module = $(this);
		current_module.addClass('curr_module');
		var off = $(this).offset();
		module_menu.css({'top':off.top,'left':off.left + $(this).outerWidth() -62}).show();
	});
	//删除模块
	$('#module_set .del').click(function(){
		var frameDom = current_module.parents('.frame');
		current_module.remove();
		current_module = null;
		module_menu.hide();
		if(frameDom.children('.module').size() == 0) {
			frameDom.children('.message').show();
		}else {
			frameDom.children('.message').hide();
		}
		return false;
	});
	//创建模块配置对话框
	$('<div id="module_config" title="模块配置"><form name="" action="" method="POST" enctype="multipart/form-data" target="configIframe"></form></div>').appendTo('body');
	module_config_dia = $('#module_config').dialog({
		modal: true,
		autoOpen: false,
		resizable: false,
		dialogClass:'module_config_dia',
		width: 400,
		buttons:[{
			text:'确认',
			click:function(){
				var actStr = '/admin/widget/update?widget_name=' + module_config_dia_content.children().attr('name');
				actStr += '&widget_id=' + module_config_dia_content.children().attr('widget');
				actStr += '&sid=' + sid;
				$('#module_config form').attr('action',actStr).submit();
				
				module_config_dia_content.html('<div style="height:290px; background:url(/inc/style/specialedit/images/load.gif) center center no-repeat;"></div>');
				module_config_dia.dialog('option','width',400);
				module_config_dia.dialog('option','position','center');
			}
		}]
	});
	module_config_dia_content = $('#module_config form');
	//编辑模块数据
	$('#module_set .data').live('click',function(){
		module_config_dia_content.html('<div style="height:290px; background:url(/inc/style/specialedit/images/load.gif) center center no-repeat;"></div>');
		module_config_dia.dialog('open');
		module_menu.hide();
		
		$.ajax({
			type:'POST',
			url:'/admin/widget/config',
			data:{
				'widget_name': current_module.attr('name'),
				'widget_id': current_module.attr('id'),
				'sid': sid
			},
			dataType:'html',
			success:function(html){
				$(html).appendTo(module_config_dia_content.empty());
			}
		});
		
		return false;
	});
	//对已存在的框架插入编辑菜单
	$('<div class="frame_menu"><ul><li><a class="slide" href="#">栏位调整</a></li><li><a class="del" href="#">删除此栏位</a></li></ul></div>').prependTo($('#content .framebox'));
	
	//模块样式对话框
	$('<div id="module_css_config" title="编辑模块样式"><form name="" action="" method="POST" enctype="multipart/form-data" target="configIframe"></form></div>').appendTo('body');
	module_css_dia_content = $('#module_css_config form');
	
	module_css_dia = $('#module_css_config').dialog({
		modal: true,
		autoOpen: false,
		resizable: false,
		dialogClass:'module_css_config dialog',
		width: 450,
		buttons:[{
			text:'提交',
			click:function(){
				//生成style标签用
				var style_str = '';
				var widget_id = current_module.attr('id');
				var val;
				//生成配置弹出框参数
				var style_setting = '';
				
				style_str += '#' + widget_id + ' {';
				//模块字体大小
				style_setting += '{"font":["';
				val = module_css_dia_content.find(':text[name=fontsize]').val();
				if(val != '' && parseInt(val) > 0) {
					style_str += 'font-size:' + val + 'px; ';
					style_setting += val;
				}
				style_setting += '","';
				//模块字体颜色
				val = module_css_dia_content.find(':text[name=fontcolor]').val();
				if(val != '') {
					style_str += 'color:' + val + '; ';
					style_setting += val;
				}
				style_setting += '"],';
				//模块边框设置
				var borders = ['top','right','bottom','left'];
				style_setting += '"borders":[';
				module_css_dia_content.find('.borderula li').each(function(idx){
					if(idx > 0) style_setting += ',';
					style_setting += '["'
					val = $(this).find('select:first').val();
					if(val != '') {
						style_str += 'border-' + borders[idx] + '-width:' + val + '; ';
						style_setting += parseInt(val);
					}
					style_setting += '","';
					val = $(this).find('select:last').val();
					if(val != '') {
						style_str += 'border-' + borders[idx] + '-style:' + val + '; ';
						style_setting += val;
					}
					style_setting += '","';
					val = $(this).find(':text').val();
					if(val != '') {
						style_str += 'border-' + borders[idx] + '-color:' + val + '; ';
						style_setting += val;
					}
					style_setting += '"]';
				});
				style_setting += '],';
				//margin设置
				style_setting += '"margin":[';
				module_css_dia_content.find(':text[name=margin]').each(function(idx){
					if(idx > 0) style_setting += ',';
					val = parseInt($(this).val());
					if(isNaN(val)) {
						val = 0;
					}
					style_setting += val;
					style_str += 'margin-' + borders[idx] + ':' + val + 'px; ';
				});
				style_setting += '],'
				//padding设置
				style_setting += '"padding":[';
				module_css_dia_content.find(':text[name=padding]').each(function(idx){
					if(idx > 0) style_setting += ',';
					val = parseInt($(this).val());
					if(isNaN(val)) {
						val = 0;
					}
					style_setting += val;
					style_str += 'padding-' + borders[idx] + ':' + val + 'px; ';
				});
				style_setting += '],'
				//背景颜色
				style_setting += '"bgcolor":"';
				val = module_css_dia_content.find(':text[name=bgcolor]').val();
				if(val != '') {
					style_str += 'background-color:' + val + '; ';
					style_setting += val;
				}
				style_setting += '",';
				//背景图片
				style_setting += '"bgimage":["';
				val = module_css_dia_content.find(':text[name=bgimage]').val();
				if(val != '') {
					style_setting += val + '","'
					style_str += 'background-image:url(\'' + val + '\'); ';
					val = module_css_dia_content.find('select[name=bgrepeat]').val();
					style_str += 'background-repeat:' + val + '; ';
					style_setting += val;
				}
				style_setting += '"],';
				style_str += '} ';
				
				
				
				style_str += '#' + widget_id + ' a {';
				//模块连接字体大小
				style_setting += '"link":["';
				val = module_css_dia_content.find(':text[name=linksize]').val();
				if(val != '' && parseInt(val) > 0) {
					style_str += 'font-size:' + val + 'px; ';
					style_setting += val;
				}
				style_setting += '","';
				//模块连接字体颜色
				val = module_css_dia_content.find(':text[name=linkcolor]').val();
				if(val != '') {
					style_str += 'color:' + val + '; ';
					style_setting += val;
				}
				style_str += '} ';
				style_setting += '"]';
				style_setting += '}';
				//alert(style_str);
				
				$.ajax({
					type:'POST',
					url:'/admin/widget/modulecss',
					data:{
						'sid':sid,
						'widget_id':widget_id,
						'module_style_setting':style_setting
					},
					dataType:'json',
					success:function(json){
						if(json.success) {
							if(style_str != '') {
								$('#' + widget_id).prev('style').remove();
								$('<style type="text/css">' + style_str + '</style>').insertBefore('#'+widget_id);
							}
							module_css_dia.dialog('close');
						}
					}
				});
			}
		}]
	});
	
	$('#module_set .css').live('click',function(){
		module_css_dia_content.html('<div style="height:290px; background:url(/inc/style/specialedit/images/load.gif) center center no-repeat;"></div>');
		module_css_dia.dialog('open');
		module_menu.hide();
		
		$.ajax({
			type:'get',
			url:'/admin/widget/modulecss',
			data:{
				'widget_name': current_module.attr('name'),
				'widget_id': current_module.attr('id'),
				'sid': sid
			},
			dataType:'html',
			success:function(html){
				$(html).appendTo(module_css_dia_content.empty());
			}
		});
		
		return false;
	});
	
	
	
	
	
	
/**
 *
 * 对于再编辑的专题的初始化部分
 *
*/

	//对于已存在的模块插入.message提示div
	$('<div class="message">请插入功能模块</div>').appendTo($('#content .frame'));
	
	//关闭模块框架不为空的div.message
	$('#content .frame').each(function(){
		if($(this).children('.module').size() != 0) {
			$(this).children('.message').hide();
		}
	});
	
	//启动现有模块排序功能
	resetSort();
	
	
	
	
	
/**
 *
 * 框架操作
 *
*/

	//插入分栏
	$('#top_control .control_layout li a').click(function(){
		$(this).next('.framebox').clone().appendTo($('#content'));
		resetSort();
		//scrollScreen('#content .framebox:last');
		return false;
	});
	//分栏排序
	content_sort = $("#content").sortable({
		placeholder: "ui-state-frame-highlight"
	});
	//编辑框架标题
	$('#content .mb-contit .mb-contit-left,#content .mb-contit .mb-contit-right').live('click',function(){
		if($(this).find(':text').size() == 0) {
			$(this).html('<input type="text" value="'+$(this).text()+'" />');
			$(this).children(':text').focus().blur(function(){
				$(this).parent().html($(this).val());
			});
		}
	});
	//删除框架
	$('#content .framebox .frame_menu .del').live('click',function(){
		module_menu.hide();
		$(this).parents('.framebox').remove();
		return false;
	});
	//框架栏位宽度调整
	$('#content .framebox .frame_menu .slide').live('click',function(){
		module_menu.hide();
		if($(this).parents('.framebox').hasClass('framebox2')) {
			slider2_bar.insertAfter($(this).parents('.framebox').children('.mb-contit'));
			var $percent = $(this).parents('.framebox').children('.frame:eq(0)').attr('percent');
			current_slide = slider2_bar.slider({
				value:$percent,
				min: 0,
				max: 100,
				slide: function( event, ui ) {
					var leftW = parseInt(ui.value);
					var rightW = 100 - leftW;
					$(this).nextAll('.frame').eq(0).css('width',leftW + '%').attr('percent',leftW).end().eq(1).css('width',rightW + '%');
				}
			});
		}else if($(this).parents('.framebox').hasClass('framebox3')) {
			slider3_bar.insertAfter($(this).parents('.framebox').children('.mb-contit'));
			var $percent = [];
			$percent[0] = $(this).parents('.framebox').children('.frame:eq(0)').attr('percent');
			$percent[1] = $(this).parents('.framebox').children('.frame:eq(1)').attr('percent');
			current_slide = slider3_bar.slider({
				min: 0,
				max: 100,
				values: $percent,
				slide: function( event, ui ) {
					var leftW = parseInt(ui.values[0]);
					var midW = parseInt(ui.values[1]) - leftW;
					var rightW = 100 - leftW - midW;
					$(this).nextAll('.frame').eq(0).css('width', leftW + '%').attr('percent',leftW).end().eq(1).css('width', midW + '%').attr('percent',parseInt(ui.values[1])).end().eq(2).css('width', rightW + '%');
				}
			});
		}
		return false;
	});
	
	
	
	
	
/**
 *
 * 模块操作
 *
*/

	//插入模块及排序
	$('#top_control .control_module li a').click(function(){
		var widgetId = '_widget_' + (widget_id++);
		var moduleDom = $('<div class="module" id="' + widgetId + '" name="' + $(this).attr('wid') + '"></div>').appendTo($('#content .frame:last'));
	
		$.ajax({
			type:'POST',
			url:'/admin/widget/show',
			data:{
				'widget_name': $(this).attr('wid'),
				'widget_id': widgetId,
				'sid':sid
			},
			dataType:'html',
			success:function(html){
				$($(html).html()).appendTo($('#' + $(html).attr('widget')).empty());
			}
		});   
		$('#content .frame:last').children('.message').hide();
		
		//模块排序
		resetSort();
		$('#' + widgetId).mouseenter();
		//scrollScreen('#' + widgetId)
		return false;
	});
	
	
	
/**
 *
 * 保存功能
 *
*/

	//取消保存
	$('#top_control .control_save .cancel').click(function(){
		location.reload();
	});
	//保存操作
	$('#top_control .control_save .save').click(function(){
		var json_str = '{';
		//皮肤id
		json_str += '"skin_id":"' + $('#top_control .control_skin li.active').attr('skid') + '",';
		//主题id
		json_str += '"sid":' + sid + ',';
		//下一个模块id
		json_str += '"widget_id":' + (++widget_id) + ',';
		//keywords & description & banner
		json_str += '"keywords":"' + $('meta[name=keywords]').attr('content') + '",';
		json_str += '"description":"' + $('meta[name=description]').attr('content') + '",';
		json_str += '"banner":{"src":"'+$('.mb-header-img img').attr('src')+'","title":"'+$('.mb-header-img img').attr('title')+'","style":"'+$('.mb-header-img img').attr('style')+'"},';
		//模块框架部分
		json_str += '"content":[';
		//循环framebox
		$('#content .framebox').each(function(idx){
			if(idx > 0) {
				json_str += ',';
			}
			json_str += '{"type":'
			if($(this).is('.framebox1')) {
				json_str += '1,';
			}else if($(this).is('.framebox2')) {
				json_str += '2,';
			}else if($(this).is('.framebox3')) {
				json_str += '3,';
			}
			json_str += '"title_left":"' + $(this).find('.mb-contit-left').text() + '",';
			json_str += '"title_right":"' + $(this).find('.mb-contit-right').text() + '",';
			json_str += '"frames":['
			//循环frame
			$(this).find('.frame').each(function(idx){
				if(idx > 0) {
					json_str += ',';
				}
				json_str += '{"style":"' + $(this).attr('style') + '",';
				json_str += '"percent":"' + $(this).attr('percent') + '",';
				json_str += '"widget":[';
				//循环模块
				$(this).find('.module').each(function(idx){
					if(idx > 0) {
						json_str += ',';
					}
					json_str += '{';
					json_str += '"id":"' + $(this).attr('id') + '",';
					json_str += '"name":"' + $(this).attr('name') + '"';
					//if($(this).prev('style').size() != 0) {
					//	json_str += '"style":"' + $.trim($(this).prev('style')[0].innerHTML) + '"';
					//}else {
					//	json_str += '"style":""';
					//}
					//json_str += '"style":"' + $(this).prev('style').text() + '"';
					json_str += '}';
				});
				json_str += ']';
				json_str += '}'
			});
			
			json_str += ']}';
		});
		json_str += ']'
		json_str += '}';
		//提交数据
		var form = $('<form name="" action="" method="POST" style="position:absolute;top:-99999px;left:-999999px;"><textarea name="data"></textarea><input type="text" value="" name="modules" /></form>').appendTo('body');
		form.find('textarea').val(json_str);
		//提取模块id
		var id_str = '';
		$('#content .module').each(function(idx){
			if(idx > 0) id_str += ',';
			id_str += $(this).attr('id');
		});
		form.find(':text').val(id_str);
		
		form.submit();
	});

}




//$('<div id="edit_page_btn"><a href="#">编辑页面</a></div>').prependTo($('body'));
//$('#edit_page_btn a').click(function(){
	//$(this).parent().hide();
	$('<div id="top_control"><p style="height:70px;background:url(/inc/style/specialedit/images/loading.gif) no-repeat center center"></p></div>').prependTo($('body'));
	$('#top_control').load('/admin/special/controlbar/',{},function() {
		//运行各种编辑功能
		init();
	});
	//return false;
//});

















/*function() {

*
 *
 * 编辑条及弹出框
 *

//弹出框选择模板
var select_skin_dialog = $('#select_skin_dialog').dialog({
	modal: true,
	autoOpen: true,
	resizable: false,
	dialogClass:'select_skin_dialog',
	height: 500,
	width: 500,
	buttons:[{
		text:'确认',
		click:function(){
			var skinId = $('#select_skin_dialog .skin_list .on').attr('skin');
			setting['skin'] = skinId;
			$('.skin_css').attr('href','/inc/skin/skin'+skinId+'/style.css');
			select_skin_dialog.dialog('close');
		}
	}]
});
	
}*/