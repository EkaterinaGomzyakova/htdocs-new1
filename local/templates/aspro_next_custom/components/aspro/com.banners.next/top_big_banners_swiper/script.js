$(function () {
    new Swiper('.banner-top-swiper', {
        direction: 'horizontal',
        loop: true,
        autoplay: {
            delay: 4000,
        },
        pagination: {
            el: '.banner-top-swiper__pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.banner-top-swiper__button-next',
            prevEl: '.banner-top-swiper__button-prev',
        }
    });
});
