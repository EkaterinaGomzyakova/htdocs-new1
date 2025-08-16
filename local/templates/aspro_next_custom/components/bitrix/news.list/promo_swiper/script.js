const promoMainSwiper = new Swiper('.promo-main-swiper', {
	direction: 'horizontal',
	centeredSlides: true,
	slidesPerView: 2,
	spaceBetween: 5,
	dots: false,
	loop: false,
	breakpoints: {
		630: {
			slidesPerView: 3,
			spaceBetween: 20,
			centeredSlides: true,
			centeredSlidesBounds: false,
			loop: false,
			centerInsufficientSlides: true,
		},
		1000: {
			slidesPerView: 3,
			spaceBetween: 50,
			centeredSlides: false,
			centeredSlidesBounds: true,
			loop: false,
			centerInsufficientSlides: true,
		},
		1300: {
			slidesPerView: 4,
			spaceBetween: 70,
			centeredSlides: false,
			centeredSlidesBounds: true,
			loop: false,
			centerInsufficientSlides: true,
		}
	},

	navigation: {
		nextEl: '.promo-main-swiper__button-next',
		prevEl: '.promo-main-swiper__button-prev',
	},
});
