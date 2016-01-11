$(function(){
	var formDom = $('#special_data_form');
	var sortByDom = formDom.find('[name=sortBy]');
	$('.cs_bt_sort li>a').click(function(){
		sortByDom.val($(this).attr('sort'));
		formDom.submit();
		return false;
	});
});