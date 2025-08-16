(function (window) {
    initCarouselDotsOnHover();
})(window);

function initCarouselDotsOnHover() {
    let dots = document.querySelectorAll('.catalog-section-carousel-dot');
    if (dots) {
        dots.forEach((dot) => {
            dot.addEventListener('mouseenter', () => {
                dot.parentElement.parentElement.querySelectorAll('.catalog-section-carousel-images img').forEach((image) => {
                    image.style.display = 'none';
                });

                document.querySelector('[data-image="' + dot.dataset.key + '"]').style.display = 'block';
            });
        });
    }
}