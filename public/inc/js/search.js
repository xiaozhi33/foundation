$(function(){
	//导航折叠
	$('.cs_search_nav_title>a.close').parent().next().find('.cs_search_nav_pic').animate({width:'toggle'},0);
	$('.cs_search_nav_title>a').click(function(){
		if($(this).hasClass('close')) {
			var ulDom = $(this).removeClass('close').parent().next();
			ulDom.show();
			ulDom.find('.cs_search_nav_pic').animate({width:'toggle'},300);
			if($(this).hasClass('source')) {
				$('.cs_search_canvas_con_list').show();
			}
		}else {
			var ulDom = $(this).addClass('close').parent().next();
			ulDom.hide();
			ulDom.find('.cs_search_nav_pic').animate({width:'toggle'},0);
			if($(this).hasClass('source')) {
				$('.cs_search_canvas_con_list').hide();
			}
		}
		return false;
	}).focus(function(){
		$(this).blur();
	});
	
	//翻页
	$('.cs_pagebar_right').find('a').click(function(){
		var page = $(this).attr('href');
		page = parseInt(page.slice(page.indexOf('offset=') + 7));
		$('input[name=offset]').val(page);
		$('#cs_topseach_seartab').submit();
		return false;
	});
	
	//filter drill down
	$('.cs_search_nav_con>li').click(function(){
		$('input[name=drillDown]').val($(this).find('.nav').text());
		$('#cs_topseach_seartab').submit();
	});
	$('.cs_search_canvas_con_list>li>a').click(function(){
		$('input[name=drillDown]').val($(this).find('.nav').text());
		$('#cs_topseach_seartab').submit();
		return false;
	});
	
	//filter drill up
	$('a.drill_up').click(function(){
		$('input[name=drillUp]').val($(this).find('.nav').text());
		$('#cs_topseach_seartab').submit();
		return false;
	});
	
	//排序
	$('.cs_bt_sort li>a').click(function(){
		$('input[name=sortBy]').val($(this).attr('sort'));
		$('#cs_topseach_seartab').submit();
		return false;
	});
	
	//视图选择
	$('.cs_secbar li>a').click(function(){
		return false;
	});
	
	//公司信息
	$('#companyIntro').click(function(){
		$('#cs_detail_win').show();
		return false;
	});
	$('#close').click(function(){
		$('#cs_detail_win').hide();
		return false;
	});
	
	//查询预处理
	$('#cs_topseach_seartab').submit(function(){
		var queryDom = $(this).find('input[name=query]');
		if(queryDom.val() != queryDom.attr('origin')) {
			$(this).find('[name=navigation]').val('');
		}
	});
});