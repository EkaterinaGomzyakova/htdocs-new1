const promoMainSwiper = new Swiper('.promo-main-swiper', {
	direction: 'horizontal',

	// --- ГЛАВНЫЕ ИЗМЕНЕНИЯ (ДЛЯ МОБИЛЬНЫХ УСТРОЙСТВ) ---
	slidesPerView: 'auto',  // <-- Включаем режим автоматической ширины. Слайдер будет брать ширину из CSS (наши 831px).
	spaceBetween: 48,       // <-- Устанавливаем нужный вам отступ.
	centeredSlides: true,   // <-- Оставляем, чтобы на мобильных слайд был по центру.
	// ---------------------------------
	
	loop: false,

	breakpoints: {
		630: {
			// --- ИЗМЕНЕНИЯ ДЛЯ ПЛАНШЕТОВ ---
			slidesPerView: 'auto',      // <-- То же самое
			spaceBetween: 48,           // <-- То же самое
			centeredSlides: true,
			// ------------------------------
			
			centeredSlidesBounds: false,
			centerInsufficientSlides: true,
		},
		1000: {
			// --- ИЗМЕНЕНИЯ ДЛЯ ДЕСКТОПА ---
			slidesPerView: 'auto',      // <-- То же самое
			spaceBetween: 48,           // <-- То же самое
			centeredSlides: false,      // <-- На десктопе центрирование уже не нужно, слайдер начнется слева.
			// ------------------------------
			
			centeredSlidesBounds: true,
			centerInsufficientSlides: true,
		},
		1300: {
			// --- ИЗМЕНЕНИЯ ДЛЯ БОЛЬШИХ ДЕСКТОПОВ ---
			slidesPerView: 'auto',      // <-- То же самое
			spaceBetween: 48,           // <-- То же самое
			centeredSlides: false,
			// ------------------------------
			
			centeredSlidesBounds: true,
			centerInsufficientSlides: true,
		}
	},

	// Навигация остается без изменений
	navigation: {
		nextEl: '.promo-main-swiper__button-next',
		prevEl: '.promo-main-swiper__button-prev',
	},
});