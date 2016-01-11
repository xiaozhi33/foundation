$(function(){
	var pager = $('<div id="flow_pager"></div>').appendTo('body');
	var load = $('#loading');
	var distance = 300;
	var loadTop = load.offset().top - distance;
	var loading_mark = false;
	var $top = $(window).height();
	var cur_offset = 1;
	var ul_dom = $('.cs_jdsj_con_nrurl').eq(0);
	pager.css({top:$top,width:1,height:1,right:0});
	$(window).scroll(function(){
		$top = $(window).scrollTop() + $(window).height();
		pager.css({top: $top});
		if($top > loadTop) {
			if(!loading_mark) {
				loading_mark = true;
				load.show();
				$.ajax({
					type:'POST',
					url:request_url,
					data:{
						'pageID':++cur_offset
					},
					dataType:'html',
					success:function(html){
						if(html) {
							$(html).appendTo(ul_dom);
							loadTop = load.offset().top - distance;
							loading_mark = false;
						}
						load.hide();
					}
				});
			}
		}
	});
});