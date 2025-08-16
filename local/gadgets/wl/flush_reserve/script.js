class FlushReserves {
    constructor() {
        this.$form = document.querySelector('[data-form-flush-reserves]');
        this.$errorBlock = document.querySelector('[data-errors-block]');
        this.$successBlock = document.querySelector('[data-success-block]');
        this.$saveButton = this.$form.querySelector('[name=flushButton]');
        this.$saveButton.addEventListener('click', this.flushReserves.bind(this));
    }

    flushReserves(e) {
        e.preventDefault();

        const result = confirm('Все заказы отгружены?');
        if (result) {
            BX.ajax.runAction('wl:snailshop.api.Goods.flushAllReserves', {
                data: {}
            }).then(response => {
                if (response.data) {
                    this.$errorBlock.style.display = "none";
                    this.$successBlock.style.display = "block";
                    console.log(response.data);
                }
            }, response => {
                this.$errorBlock.innerHTML = response.errors[0].message;
                this.$errorBlock.style.display = "block";
                this.$successBlock.style.display = "none";
            })
        }
        return false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FlushReserves();
});