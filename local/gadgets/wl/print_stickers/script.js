class GenerateCertificate {
    constructor() {
        this.$form = document.querySelector('[data-form-generate-ceartificate]');
        this.$fieldCoupon = this.$form.querySelector('[data-field-coupon]')
        this.$error = this.$form.querySelector('[data-error]')
        this.$info = this.$form.querySelector('[data-info]')
        this.$form.addEventListener('submit', this.generate.bind(this));
        this.$fieldCoupon.addEventListener('input', this.showCouponInfo.bind(this));
        this.$btnSend = this.$form.querySelector('[data-btn-generate]');
    }

    // language=HTML
    showCouponInfo(e) {
        let query = e.target.value;
        this.$btnSend.disabled = true;
        if (query.length > 0) {
            BX.ajax.runAction('wl:snailshop.api.generatecertificate.certificateInfo', {
                data: {
                    text: query
                }
            }).then(response => {
                this.$error.style.display = 'none';
                this.$info.style.display = 'block';
                let template = `
                    <table class="bx-gadgets-info-site-table">
                        <tbody>
                        <tr>
                            <td class="bx-gadget-gray">Название скидки</td>
                            <td>${response.data.DISCOUNT_NAME}</td>
                        </tr>
                        <tr>
                            <td class="bx-gadget-gray">Номинал</td>
                            <td>${response.data.SUM_DISPLAY}</td>
                            <input type="hidden" name="sum" value="${response.data.SUM}" >
                        </tr>
                        <tr>
                            <td class="bx-gadget-gray">Активность</td>
                            <td class="status ${response.data.ACTIVE == 'Y'?'success':'fail'}">${response.data.ACTIVE_DISPLAY}</td>
                        </tr>
                        <tr>
                            <td class="bx-gadget-gray">ID купона</td>
                            <td>${response.data.ID}</td>
                        </tr>
                        <tr>
                            <td class="bx-gadget-gray">Дата создания</td>
                            <td>${response.data.DATE_CREATE}</td>
                        </tr>
                        <tr>
                            <td class="bx-gadget-gray">Комментарий</td>
                            <td style="white-space: pre">${response.data.DESCRIPTION}</td>
                        </tr>
                        </tbody>
                    </table>
                `;
                this.$info.innerHTML = template;
                this.$btnSend.disabled = false;
            }, response => {
                this.$info.style.display = 'none';
                this.$error.style.display = 'block';
                this.$error.innerHTML = response.errors[0].message;
            })
        } else {
            this.$error.style.display = 'none';
            this.$info.style.display = 'none';
            this.$btnSend.disabled = true;
        }
    }

    generate(e) {
        e.preventDefault();
        this.$error.style.display = 'none';
        let formData = new FormData(e.target);
        BX.ajax.runAction('wl:snailshop.api.generatecertificate.generate', {
            data: formData
        }).then(response => {
            let link = document.createElement('a');
            link.href = response.data.link;
            link.download = response.data.file_name;
            document.body.appendChild(link);
            link.click();
            link.remove();
        }, response => {
            this.$error.style.display = 'block';
            this.$error.innerHTML = response.errors[0].message;
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new GenerateCertificate();
});