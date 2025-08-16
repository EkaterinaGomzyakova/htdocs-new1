$(document).ready(function(){
	$('.owl-carousel').owlCarousel({
		loop: true,
		margin: 10,
		nav: true,
		autopWidth: true,
		navText: [
			'<div class="flex-nav-next"><a class="flex-prev" href="#"></a></div>',
			'<div class="flex-nav-prev"><a class="flex-next" href="#"></a></div>'
		],
		responsive:{
			0: {
				items: 1
			},
			768:{
				items: 2
			}
		}
	});

	$('body').on('click', '.owl-nav a', function (e) {
		e.preventDefault();
	})
});