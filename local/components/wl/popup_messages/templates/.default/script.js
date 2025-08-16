class PopupMessage {
    constructor(params) {
        this.params = params;
        $('#' + this.params.component_id + '_popup .modal').modal('show');

        let self = this;
        $(`.${this.params.component_id}-confirm`).on('click', function (e) {
            e.stopPropagation();
            self.markReadAction().then(() => {
                location.href = $(e.target).attr('href');
            })

            return false;
        });

        $('#' + this.params.component_id + '_popup .modal').on('hidden.bs.modal', function (e) {
            self.markReadAction();
        });
    }

    markReadAction() {
        $('#' + this.params.component_id + '_popup .modal').modal('hide');
        return new Promise((resolve) => {
            BX.ajax.runComponentAction("wl:popup_messages", "markRead", {
                mode: "class",
                data: {
                    id: this.params.id
                }
            }).then(response => {
                resolve();
            })
        })
    }
}