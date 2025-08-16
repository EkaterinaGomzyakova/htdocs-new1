$(document).ready(function () {
    $('#previous-party').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        autoHeight: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1200: {
                items: 2
            }
        }
    });

    $('#order').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            data: $(this).serialize(),
            url: $(this).attr('action'),
            dataType: 'json',
            encode: true,
        }).done(function (data) {
            if (data.SUCCESS) {
                $('#form-error').hide();
                location.href = data.ORDER_LINK;
            } else if (data.ERROR) {
                $('#form-error').show();
                $('#form-error').html(data.MESSAGE);
            }
        });

        return false;
    });

    $().fancybox({
        selector : '.owl-item:not(.cloned) a'
    });

    $('#PHONE').inputmask('phone');
});