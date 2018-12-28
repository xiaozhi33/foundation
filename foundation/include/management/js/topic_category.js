//添加子分类
var addSub_dia = $('#addSub_dia').dialog({
	modal: true,
	autoOpen: false,
	resizable: false,
	dialogClass:'addSub_dia dialog',
	width: 450,
	buttons:[{
		text:'提交',
		click:function(){
			$('#addSub_dia .content input[name=image]').val($('#addSub_dia .content .cate_image>img').attr('src'));
			$('#addSub_dia #sampleform2').submit();
		}
	}]
});
$('.addSubCate').click(function(){
	$('#addSub_dia .content .cate_image select option:first').attr('selected',true);
	$('#addSub_dia .content .cate_image a.btn').hide();
	var img_path = $('#addSub_dia .content .cate_image select option:first').attr('value');
	img_path = img_path;
	
	$('#addSub_dia input[name=pid]').val($(this).attr('cid'));
	$('#addSub_dia input[name=image]').val(img_path);
	$('#addSub_dia input[name=t_cate_name]').val('');
	$('#addSub_dia input[name=cate_order]').val('0');
	$('#addSub_dia textarea[name=t_cate_desc]').val('');
	$('#addSub_dia .content .cate_image>img').attr('src',img_path);
	
	$('#addSub_dia #sampleform2').attr('action','/management/category/add-subcate');
	
	addSub_dia.dialog('option','title',$(this).text());
	addSub_dia.dialog('open');
	return false;
});
//分类图片
$('#addSub_dia .content .cate_image select').change(function(){
	if($(this).val() == 0) {
		$('#addSub_dia .content .cate_image>img').attr('src',$('#addSub_dia .content input[name=image]').val());
		$('#addSub_dia .content .cate_image a.btn').css('display','inline-block');
	}else {
		$('#addSub_dia .content .cate_image>img').attr('src', $(this).val());
		$('#addSub_dia .content .cate_image a.btn').hide();
	}
});
$('#addSub_dia .content .cate_image a.file').click(function(){
	$('#addSub_dia .content .cate_image input[name="topic_category_image"]').click();
	return false;
});
$('#addSub_dia .content .cate_image a.submit').click(function(){
	$(this).parents('.cate_image:first').children('img').attr('src','').css('background','url(/management/img/nyro/ajaxLoader.gif) no-repeat center center');
	$(this).parents('form:first').submit();
	
	return false;
});
//编辑分类
$('.tree-cate .edit').click(function(){
	var cate_item = $(this).parents('.cate_item:first');
	var image_path = cate_item.children('img').attr('src');
	
	$('#addSub_dia input[name=image]').val(image_path);
	$('#addSub_dia input[name=t_cate_name]').val(cate_item.children('.c_name').text());
	$('#addSub_dia textarea[name=t_cate_desc]').val(cate_item.children('.c_desc').text());
	$('#addSub_dia input[name=cate_order]').val(cate_item.children('.order').attr('order'));
	$('#addSub_dia .cate_image>img').attr('src',image_path);
	
	$('#addSub_dia #sampleform2').attr('action','/management/category/edit-cate?cid=' + $(this).attr('cid'));
	addSub_dia.dialog('option','title',$(this).text());
	addSub_dia.dialog('open');
	return false;
});
//删除分类
$('.tree-cate .del').click(function(){
	if(confirm('您确认要进行删除操作？')) {
		$('#target_iframe').attr('src','/management/category/del-category?cid=' + $(this).attr('cid'));
	}
	return false;
});
//折叠树
$('.tree-cate ul li.collapsable div.hitarea').click(function(){
	if($(this).hasClass('collapsable-hitarea')) {
		$(this).removeClass('collapsable-hitarea').addClass('expandable-hitarea');
		$(this).nextAll('ul').hide();
	}else {
		$(this).removeClass('expandable-hitarea').addClass('collapsable-hitarea');
		$(this).nextAll('ul').show();
	}
});