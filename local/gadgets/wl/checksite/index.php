<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<form id="checkForm" action="/local/ajax/check.php">
    <input type="hidden" name="method" value="ajax">
    <input type="hidden" name="action" value="check">
    <input type="submit" class="adm-btn-save" value="Проверка" style="margin-bottom: 15px;">
</form>

<div id="checkFormResult"></div>

<script>
    $('#checkForm').on("submit", function(e) {
        e.preventDefault();
        BX.showWait(document.querySelector('#checkForm .adm-btn-save'));

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(data) {
                $('#checkFormResult').html(data);
            },
            complete: function() {
                BX.closeWait(document.querySelector('#checkForm .adm-btn-save'));

                $('a[data-method]').on('click', function(e) {
                    e.preventDefault();
                    let method = e.target.dataset.method;
                    BX.ajax.runComponentAction('clanbeauty:check',
                            'updateSection', { // Вызывается без постфикса Action
                                mode: 'class',
                                data: {
                                    methodName: method,
                                },
                            })
                        .then(function(response) {
                            if (response.status === "success") {
                                let mainContainer = e.target.parentElement.parentElement;
                                let errorsContainer = mainContainer.querySelector('.check-errors');

                                mainContainer.classList.remove('check-success', 'check-error');
                                if (response.data.isSuccess) {
                                    mainContainer.classList.add('check-success');
                                    if (errorsContainer) {
                                        errorsContainer.remove();
                                    }
                                } else {
                                    mainContainer.classList.add('check-error');
                                    if (response.data.items) {
                                        errorsContainer.innerHTML = '';
                                        response.data.items.forEach((item) => {
                                            errorsContainer.innerHTML += `<a href="${item.url}" target="_blank">${item.name}</a><br>`;
                                        });
                                    }
                                }
                            }
                        });
                });
            }
        });
    });
</script>