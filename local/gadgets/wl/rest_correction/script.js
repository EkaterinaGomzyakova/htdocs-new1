class RestCorrection {
    constructor() {
        this.$form = document.querySelector('[data-form-rest-correction]');
        this.$productId = this.$form.querySelector('[data-field-product-id]');
        this.$productId.addEventListener('input', this.hideInfoAndSaveButton.bind(this));

        this.$storesBlock = this.$form.querySelector('[data-fields-stores]');


        this.$errorBlock = this.$form.querySelector('[data-error]');
        this.$infoBlock = this.$form.querySelector('[data-info]');
        this.$productInfoBlock = this.$form.querySelector('[data-product-info]');
        this.$productNameBlock = this.$form.querySelector('[data-product-name]');
        this.$totalQuantityBlock = this.$form.querySelector('[data-total-quantity]');

        this.$saveButton = this.$form.querySelector('[name=save]');
        this.$saveButton.addEventListener('click', this.save.bind(this));

        this.$readButton = this.$form.querySelector('[name=read]');
        this.$readButton.addEventListener('click', this.read.bind(this));
    }

    hideInfoAndSaveButton(e) {
        this.$saveButton.style.display = 'none';
        this.$productInfoBlock.style.display = 'none';
        this.$storesBlock.innerHTML = '';
    }

    read(e) {
        if (this.$productId.value > 0) {
            BX.ajax.runAction('wl:snailshop.api.Goods.getGadgetProductInfo', {
                data: {
                    productId: this.$productId.value
                }
            }).then(response => {
                this.$errorBlock.style.display = 'none';

                if (response.data) {
                    this.$storesBlock.innerHTML = '';
                    this.$infoBlock.style.display = 'none';

                    var totalQuantity = 0;
                    for (let storeId in response.data.STORES) {
                        var storeDiv = document.createElement('div');
                        storeDiv.classList.add('input-group');

                        var storeName = document.createElement('span');
                        storeName.innerHTML = response.data.STORES[storeId].STORE_NAME + ": ";
                        storeDiv.appendChild(storeName);

                        var storeInput = document.createElement('input');
                        storeInput.type = 'number';
                        storeInput.id = 'amount-' + storeId;
                        storeInput.name = 'amount[' + storeId + ']';
                        storeInput.value = response.data.STORES[storeId].AMOUNT
                        storeDiv.appendChild(storeInput);

                        this.$storesBlock.appendChild(storeDiv);

                        totalQuantity += parseInt(response.data.STORES[storeId].AMOUNT);
                    }

                    this.$productInfoBlock.style.display = 'block';
                    this.$saveButton.style.display = 'inline-block';
                    this.$totalQuantityBlock.innerHTML = totalQuantity;
                    this.$productNameBlock.innerHTML = response.data.BASE.NAME;
                }
            }, response => {
                this.$infoBlock.style.display = 'none';
                this.$errorBlock.style.display = 'block';
                this.$errorBlock.innerHTML = response.errors[0].message;
            })
        } else {
            this.$errorBlock.style.display = 'block';
            this.$errorBlock.innerHTML = 'Введите число';
        }
    }

    save(e) {
        if (this.$productId.value > 0) {
            let formData = new FormData(this.$form);

            BX.ajax.runAction('wl:snailshop.api.Goods.saveGadgetProductInfo', {
                data: formData
            }).then(response => {
                this.$errorBlock.style.display = 'none';

                if (response.status == "success") {
                    this.$infoBlock.style.display = 'block';
                    this.hideInfoAndSaveButton(e);
                }
            }, response => {
                this.$infoBlock.style.display = 'none';
                this.$errorBlock.style.display = 'block';
                this.$errorBlock.innerHTML = response.errors[0].message;
            })
        } else {
            this.$errorBlock.style.display = 'block';
            this.$errorBlock.innerHTML = 'Введите число';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new RestCorrection();
});