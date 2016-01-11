if(cur_year) {
	$(function(){
		//source 选择
		var sources = $(':radio[name=sources]');
		var sourceRadio = $(':checkbox.source');
		sources.click(function(){
			if($(this).val() == 'All' && $(this).attr('checked')) {
				sourceRadio.attr('checked',true);
			}else {
				sourceRadio.attr('checked',false);
			}
		});
		
		sourceRadio.click(function(){
			var flag = true;
			sourceRadio.each(function(){
				if(!$(this).attr('checked')) {
					flag = false;
					return false;
				}
			});
			sources.attr('checked',false);
			if(flag) {
				sources.eq(0).attr('checked',true);
			}else {
				sources.eq(1).attr('checked',true);
			}
		});
		
		//时间范围日历
		$('.datetime').datepicker({
			'changeMonth': true,
			'changeYear': true,
			'dayNamesMin' : ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
			'yearRange':'1990:' + cur_year,
			'dateFormat' : 'yy-mm-dd'
		}).focus(function(){
			$(':radio[name=time_range]').attr('checked',false).filter('[value=range]').attr('checked',true);
		});
		
		$(':reset').click(function(){
			$(':text').not('[name=time_start]').val('');
			$(':radio[name=time_range]').attr('checked',false).eq(0).attr('checked',true);
			$(':radio[name=sortBy]').attr('checked',false).eq(0).attr('checked',true);
			
			
			sources.attr('checked',false).eq(0).attr('checked',true);
			sourceRadio.attr('checked',true);
		});
		
		$('form').submit(function(){
			if(sourceRadio.filter(':checked').size() == 0) {
				alert('Please select a source or sources.');
				return false;
			}
			if($('#range_time').attr('checked')) {
				var time_start = $(':text[name=time_start]').val();
				var time_end = $(':text[name=time_end]').val();
				if(!time_start.match(/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/g)) {
					alert('Start date is not correct, re-enter please.');
					return false;
				}
				if(!time_end.match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/g)) {
					alert('End date is not correct, re-enter please.');
					return false;
				}
				if(time_start.localeCompare(time_end) == 1) {
					alert('Date range is not correct. The start date should be before or the same as end date, re-enter please.');
					return false;
				}
			}
			
			//source[other] 设置
			if($(':checkbox.other').attr('checked') == 'checked') {
				var source = '';
				$(':checkbox.source:not(":checked")').each(function(idx){
					if(idx > 0) {
						source += ' ';
					}
					source += $(this).val();
				});
				if(source != '') {
					$(':checkbox.other').val(source);
				}
			}
		});
	});
}else {
	//搜索结果页部分
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
			$('#cs_topseach_seartab').attr('action','').submit();
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
			}else {
				$(this).attr('action','');
			}
		});
	});
}