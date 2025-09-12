/**
 * Универсальный инициализатор слайдеров товаров для сложных шаблонов
 * - Инициализирует слайдеры при загрузке страницы.
 * - Использует MutationObserver для отслеживания ЛЮБЫХ изменений в DOM (включая AJAX).
 * - Автоматически находит и инициализирует новые, еще не обработанные слайдеры.
 */
(function() {
    // Чтобы избежать повторного запуска этого кода, ставим флаг в window
    if (window.hasUniversalSliderInitializer) {
        return;
    }
    window.hasUniversalSliderInitializer = true;

    const initializeSliders = (context) => {
        // Ищем только те обертки, которые еще не были обработаны
        const wrappers = context.querySelectorAll('.a-products-slider-wrapper:not([data-slider-init])');

        if (wrappers.length > 0) {
            console.log(`Найдено ${wrappers.length} новых слайдеров для инициализации.`);
        }

        wrappers.forEach((wrapper, index) => {
            // Ставим метку, что этот элемент мы уже обработали
            wrapper.setAttribute('data-slider-init', 'true');

            const sliderEl = wrapper.querySelector('.a-products-slider');
            const prevEl = wrapper.querySelector('.a-slider-btn-prev');
            const nextEl = wrapper.querySelector('.a-slider-btn-next');

            if (sliderEl && prevEl && nextEl) {
                // Проверяем, не висит ли на элементе уже созданный Swiper
                if (sliderEl.swiper) return;

                // Определяем отступ между слайдами в зависимости от размера экрана
                const getSpaceBetween = () => {
                    return window.innerWidth <= 768 ? 0 : 30;
                };

                new Swiper(sliderEl, {
                    // ИЗМЕНЕНИЕ 1: Говорим Swiper брать ширину из CSS
                    slidesPerView: 'auto', 
                    
                    // ИЗМЕНЕНИЕ 2: Устанавливаем отступ между слайдами в зависимости от размера экрана
                    spaceBetween: getSpaceBetween(),

                    navigation: {
                        nextEl: nextEl,
                        prevEl: prevEl,
                    },
                    observer: true,
                    observeParents: true,
                    
                    // Обновляем отступы при изменении размера окна
                    on: {
                        resize: function() {
                            this.params.spaceBetween = getSpaceBetween();
                            this.update();
                        }
                    }
                });
            }
        });
    };

    // 1. Запускаем при первой загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        initializeSliders(document.body);
    });

    // 2. Создаем "главного наблюдателя", который следит за всей областью контента
    const mainContentNode = document.getElementById('content') || document.body;
    
    const observer = new MutationObserver((mutationsList) => {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(() => {
                    initializeSliders(mainContentNode);
                }, 100);
                return; 
            }
        }
    });

    // 3. Запускаем наблюдателя
    observer.observe(mainContentNode, {
        childList: true,
        subtree: true
    });

    console.log('Универсальный наблюдатель за слайдерами запущен.');

})();