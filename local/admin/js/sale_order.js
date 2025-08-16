function showCourierMsgDialog(id) {
    $.get('/local/ajax/get-order-info.php', {order_id: id}, function (response) {
        var Dialog = new BX.CDialog({
            title: "Сообщение курьеру",
            content: response,
            icon: 'head-block',
            resizable: false,
            draggable: false,
            height: '140',
            width: '400',
            buttons: [BX.CDialog.btnClose]
        });
        Dialog.Show();
    });
}

function showConsentProcesssingPersonalDataDialog(userID) {
    let content = `
            <div class="confirm-personal-data adm-workarea">
                <a href="/include/license_print.php?user=` + userID + `" target="_blank" class="adm-btn">Распечатать</a>
                <button class="adm-btn adm-btn-save" onclick="confirmUserConsentProcesssing(` + userID + `)">Подтвердить согласие</button>              
               
            </div>
        `;
    let Dialog = new BX.CDialog({
        title: "Согласие на обработку персональных данных",
        content: content,
        icon: 'head-block',
        resizable: false,
        draggable: true,
        height: '150',
        width: '400',
    });
    Dialog.Show();
}

function confirmUserConsentProcesssing(userID) {
    $.post('/local/ajax/sale-order.php', {action: 'confirmConsentProcessingPersonalData', USER_ID: userID}, function (response) {
        BX.WindowManager.Get().Close();
        location.reload();
    }, 'json')

}