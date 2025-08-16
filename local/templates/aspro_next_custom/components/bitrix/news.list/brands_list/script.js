$(document).ready(function () {
    let brandFilter = {
        objects: [],
        index: {},
        andKeys: [],
        request: [],
    };
    $('.js-partner-item').each(function (index) {
        brandFilter.objects.push($(this));
        $(this).data('fiterSet').forEach(function (e) {
            if (!brandFilter.index[e]) {
                brandFilter.index[e] = [];
            }
            brandFilter.index[e].push(index);
        });
    });
    $('.js-brands-nav-filter').each(function () {
        brandFilter.andKeys.push($(this).data('filter'));
    });

    $('.js-brands-start').on('click', function (e) {
        e.preventDefault();
        $('.js-brands-start').removeClass('active');
        $(this).addClass('active');
        let symb = $(this).data('symb');
        brandFilter.request = brandFilter.request.filter(value => brandFilter.andKeys.includes(value));
        if (symb !== 'all') {
            brandFilter.request.push(symb);
        }
        applyBrandsFilter();
        return false;
    });

    $('.js-brands-nav-filter').on('click', function (e) {
        e.preventDefault();
        let filter = $(this).data('filter');
        let button = $(this).parents('.item');
        if (brandFilter.request.includes(filter)) {
            button.removeClass('active');
            let index = brandFilter.request.indexOf(filter);
            if (index > -1) {
                brandFilter.request.splice(index, 1);
            }
        } else {
            button.addClass('active');
            brandFilter.request.push(filter);
        }
        applyBrandsFilter();
        return false;
    });

    $('.js-brands-nav-ru').on('click', function () {
        $('.js-brand-nav-en').hide();
        $('.js-brand-nav-ru').show();
        $(this).hide();
        $('.js-brands-nav-en').show();
        return false;
    })

    $('.js-brands-nav-en').on('click', function () {
        $('.js-brand-nav-ru').hide();
        $('.js-brand-nav-en').show();
        $(this).hide();
        $('.js-brands-nav-ru').show();
        return false;
    })

    function applyBrandsFilter () {
        let arIndex = [];
        for (let i= 0; i < brandFilter.objects.length; i++) {
            arIndex.push(i);
        }
        if (brandFilter.request.length) {
            brandFilter.request.forEach(function (e) {
                if (brandFilter.index[e]) {
                    arIndex = arIndex.filter(value => brandFilter.index[e].includes(value));
                }
            });
        }
        $('[data-partner-group]').hide();
        if (arIndex.length) {
            $('[data-brand-not-found]').hide();
            $('[data-brand-is-found]').show();
            brandFilter.objects.forEach(function (e, index) {
                let found = arIndex.indexOf(index);
                if (found > -1) {
                    e.show();
                    e.prevAll("[data-partner-group]:first").show();
                } else {
                    e.hide();
                }
            });
        } else {
            $('[data-brand-not-found]').show();
            $('[data-brand-is-found]').hide();
        }
    }
})


