if (document.querySelector('#right_block_ajax') && document.querySelector('#right_block_ajax').dataset.design) {
    let sectionDesign = document.querySelector('#right_block_ajax').dataset.design;
    if (sectionDesign) {
        document.querySelector('.wrapper_inner').classList.add(sectionDesign);
    }
}

$(function () {
    $('.inline-search-show').click(function () {
        $('#title-search-input').focus()
    });

    $('[data-toggle="tooltip"]').tooltip();
    FastClick.attach(document.body);
});

//Ban Yandex Sovetink
(function (open) {
    XMLHttpRequest.prototype.open = function (method, url, async, user, pass) {

        this.addEventListener("readystatechange", function () {
            var parser = document.createElement('a')
            parser.href = url;
            if (parser.hostname == 'sovetnik.market.yandex.ru') {
                this.abort();
            }

        }, false);
        open.call(this, method, url, async, user, pass);
    };
})(XMLHttpRequest.prototype.open);

let needChangePassword = false;
let formValid = false;

$(document).on('input', '#USER_LOGIN_POPUP', checkUser);

function checkUser() {
    if ($('#USER_LOGIN_POPUP').val().length > 0) {
        let phone = $('#USER_LOGIN_POPUP').val().replace(/\D+/g, "");
        if (phone.length === 11) {
            let formatPhone = '+' + phone;
            $.post('/ajax/wl_check_auth_phone.php', { phone: formatPhone }, function (response) {
                $('#USER_LOGIN_CLEAR').val(response.user_login);
                $('[name="USER_ID"]').val(response.user_id);
                if (response.is_new_user === false) {
                    if (response.need_change_password) {
                        needChangePassword = true;
                    } else {
                        needChangePassword = false;
                    }
                }
                if (needChangePassword) {
                    $('.js-password-block').slideUp();
                    $('.js-new-password-block').slideDown();
                } else {
                    $('.js-new-password-block').slideUp();
                    $('.js-password-block').slideDown();
                }
            }, 'json');
        }
    }
}

$(document).on('submit', '#avtorization-form', function () {
    $('#avtorization-form .js-error').fadeOut();
    if (needChangePassword) {
        let data = {
            PHONE: $('#USER_LOGIN_POPUP').val(),
            USER_ID: $('[name="USER_ID"]').val(),
            NEW_USER_PASSWORD: $('[name="NEW_USER_PASSWORD"]').val(),
            NEW_USER_PASSWORD_CONFIRM: $('[name="NEW_USER_PASSWORD_CONFIRM"]').val(),
            type: 'send'
        };
        if (formValid) {
            data.type = 'confirm';
            data.CODE = $('#LOGIN_CONFIRM_CODE').val();
            $.post('/ajax/wl_change_password.php', data, function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    $('#avtorization-form .js-error').html(response.error).fadeIn();
                }
            }, 'json');
        } else {
            $.post('/ajax/wl_change_password.php', data, function (response) {
                if (response.success) {
                    $('.js-login-block').fadeOut();
                    $('.js-password-block').fadeOut();
                    $('.js-new-password-block').fadeOut();
                    $('.js-sms-code').fadeIn();
                    formValid = true;
                } else {
                    $('#avtorization-form .js-error').html(response.error).fadeIn();
                }
            }, 'json');
        }

    } else {
        $.post($(this).attr('action'), $(this).serialize(), function (html) {
            if ($(html).find('.alert').length) {
                $('#ajax_auth').html(html);
            } else {
                location.reload();
            }
        });

    }
    return false;
});

function InitPhoneMask() {
    $('input.phone').inputmask("+7 (999) 999-99-99");
}

function showFavoritesToast(message) {
    let template = `
    <div class="toast fade hide toast-favorites" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
        <div class="toast-body">
            <svg xmlns="http://www.w3.org/2000/svg" width="22.969" height="21" viewBox="0 0 22.969 21">
                <defs>
                <style>
                    .whcls-1 {
                        fill: #222;
                        fill-rule: evenodd;
                    }
                </style>
                </defs>
                <path class="whcls-1" d="M21.028,10.68L11.721,20H11.339L2.081,10.79A6.19,6.19,0,0,1,6.178,0a6.118,6.118,0,0,1,5.383,3.259A6.081,6.081,0,0,1,23.032,6.147,6.142,6.142,0,0,1,21.028,10.68ZM19.861,9.172h0l-8.176,8.163H11.369L3.278,9.29l0.01-.009A4.276,4.276,0,0,1,6.277,1.986,4.2,4.2,0,0,1,9.632,3.676l0.012-.01,0.064,0.1c0.077,0.107.142,0.22,0.208,0.334l1.692,2.716,1.479-2.462a4.23,4.23,0,0,1,.39-0.65l0.036-.06L13.52,3.653a4.173,4.173,0,0,1,3.326-1.672A4.243,4.243,0,0,1,19.861,9.172ZM22,20h1v1H22V20Zm0,0h1v1H22V20Z" transform="translate(-0.031)"></path>
            </svg>
            
            <div>${message}</div>
        </div>
    </div>
    `;

    let el = document.createElement('div');
    el.innerHTML = template;
    document.querySelector('#toast-container').append(el);
    $(el).find('.toast').toast('show');
}

