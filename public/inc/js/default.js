$(function(){
	//header搜索焦点清空
	$('#header_search_query').focus(function(){
		$(this).val('');
	});
	//header搜索提交
	$('#header_search_submit').click(function(){
		$(this).parents('form').submit();
		return false;
	});
	
	//主导航下拉
	$('.cs_nav_list>li').hover(function(){
		$(this).children('a').addClass('hover');
		$(this).children('.cs_nav_catlist').show();
	},function(){
		$(this).children('a').removeClass('hover');
		$(this).children('.cs_nav_catlist').hide();
	});
	
	//自动补全
	var cache = {};
	$( "input.autocomplete" ).autocomplete({
		minLength: 1,
		source: function(request, response) {
			if ( request.term in cache ) {
				response( cache[ request.term ] );
				return;
			}
			
			$.ajax({
				url: "/index/autocomplete?callback=?",
				dataType: "json",
				data: request,
				success: function( data ) {
					cache[ request.term ] = data;
					response( data );
				}
			});
		},
		select: function(event,ui) {
			$(this).val( ui.item.label );
			$(this).parents('form:first').find(':submit').click();
		}
	});
	
	//搜索滚动
	$('#hot_list').cycle({
		fx:"scrollUp",
		timeout:4000,
		speed:300,
		pause:true
	});
	
	//预设搜索词
	if(search_word != '') {
		$('input.search_query').focus(function(){
			if($(this).val() == search_word) {
				$(this).val('');
			}
		});
		
		$('input.search_query').blur(function(){
			if($(this).val() == '') {
				$(this).val(search_word);
			}
		});
	}
});