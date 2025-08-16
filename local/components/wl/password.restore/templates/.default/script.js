$(document).ready(function () {
    $('#formForgotPassword').on('submit', 'form', function () {
        $.post('', $(this).serialize(), function (response) {
            $('#formForgotPassword').html(response);
        })
        return false;
    })
});