$(document).ready(function(){
	if($('.item.slice-item').length)
	{
		setTimeout(function(){
			$('.item.slice-item .title').sliceHeight();
			$('.item.slice-item').sliceHeight();
		}, 100);
	}
})