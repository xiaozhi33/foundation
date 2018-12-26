$('#totop').click(function(){
	if(jQuery.browser.safari) {
		jQuery('body').animate({scrollTop:jQuery('#top').offset().top}, '300');
    }
    else{
		jQuery('html').animate({scrollTop:jQuery('#top').offset().top}, '300');
    }
	
	return false;
});