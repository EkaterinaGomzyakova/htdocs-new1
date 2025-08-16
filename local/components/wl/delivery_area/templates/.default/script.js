document.addEventListener('DOMContentLoaded', function () {
    wlAreaDelivery = new WLAreaDelivery();

    BX.addCustomEvent('onAjaxSuccess', wlAreaDelivery.onAjaxSuccess);
});

class WLAreaDelivery {
    map = null;
    area = null;
    modal = null;
    deliveryPoint = null;
    geoObject = null;
    deliveryZone = null;
    zone = null;
    address = null;

    onAjaxSuccess(){
        if(typeof BX.Sale.OrderAjaxComponent != 'undefined')
        {
            var activeSection = BX.Sale.OrderAjaxComponent.activeSectionId;

            let addressInput = document.getElementById('wl_delivery_area_address');
            let zoneInput = document.getElementById('wl_delivery_area_zone');

            if (activeSection === 'bx-soa-delivery') {
                if (addressInput != null && zoneInput != null && (addressInput.value.length === 0 || zoneInput.value.length === 0)) {
                    document.getElementById('wl_delivery_show_map_btn').click();
                }
            }

            if (addressInput != null && zoneInput != null && (addressInput.value.length === 0 || zoneInput.value.length === 0)) {
                let error = 'Необходимо указать адрес доставки';
                if (typeof (BX.Sale.OrderAjaxComponent.showBlockErrors) === 'function') {
                    BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY = error;
                    BX.Sale.OrderAjaxComponent.showBlockErrors(BX.Sale.OrderAjaxComponent.deliveryBlockNode);
                } else if (typeof (BX.Sale.OrderAjaxComponent.showError) === 'function') {
                    BX.Sale.OrderAjaxComponent.showError(BX.Sale.OrderAjaxComponent.deliveryBlockNode, error);
                }
            }
        }
    }

    showMap(options) {
        this.coordinates = options.area;
        this.buildModal();
        $(this.modal).modal({
            backdrop: 'static',
            show: true
        });
        if (!this.map) {
            ymaps.ready(() => {
                this.initMap();
            });
        }
        return false;
    }

    drawPlacemark(coordinates) {
        this.deliveryPoint.geometry.setCoordinates(coordinates);
        this.map.setCenter(coordinates);
        this.map.setZoom(15);
        let zone = this.deliveryZone.searchContaining(coordinates).get(0);
        this.zone = 'other';
        if (zone) {
            this.zone = 'lipetsk';
        }
    }

    initMap() {
        let map = this.getMap();
        try {
            let inputAddress = document.getElementById('wl_delivery_area_address');
            document.getElementById('suggestAddress').value = inputAddress.value;

            let suggestView = new ymaps.SuggestView('suggestAddress', {
                boundedBy: [[52.645174, 39.512531], [52.554689, 39.671234]],
            });
            suggestView.events.add('select', event => {
                let suggestSelectedItem = event.get('item');
                this.drawPlacemarkFromAddress(suggestSelectedItem.value);
            });

            this.drawPlacemarkFromAddress(inputAddress.value);
        } catch (e) {
            console.error(e);
        }
    }

    drawPlacemarkFromAddress(text) {
        if (!text || text.length === 0) {
            return;
        }
        ymaps.geocode(text, {
            results: 1
        }).then((res) => {
            let coordinates = res.geoObjects.get(0).geometry.getCoordinates();
            this.drawPlacemark(coordinates);
        })
    }

    getMap() {
        if (this.map == null) {
            this.map = new ymaps.Map("delivery_map", {
                center: [52.6031, 39.5708],
                zoom: 10,
                controls: ['fullscreenControl', 'zoomControl'],
                suppressMapOpenBlock: true,
            });
            let geolocationControl = new ymaps.control.GeolocationControl({
                options: {noPlacemark: true}
            });
            geolocationControl.events.add('locationchange', (event) => {
                let position = event.get('position');
                this.drawPlacemark(position);
            });
            this.map.controls.add(geolocationControl);
            this.deliveryPoint = new ymaps.GeoObject({
                geometry: {type: 'Point'},
            }, {
                preset: "twirl#yellowStretchyIcon"
            });
            this.map.geoObjects.add(this.deliveryPoint);

            let areaDelivery = new ymaps.Polygon(this.coordinates, {}, {
                editorDrawingCursor: "crosshair",
                fillColor: 'rgba(141,239,141,0.2)',
                strokeColor: 'rgba(141,239,141,0.6)',
                strokeWidth: 1
            });
            this.deliveryZone = ymaps.geoQuery([
                areaDelivery
            ]);
            this.map.geoObjects.add(areaDelivery);
        }
        return this.map;
    }

    buildModal() {
        if (document.querySelector('#wl_delivery_map_modal') == null) {
            this.modal = document.createElement('div');
            this.modal.setAttribute('id', 'wl_delivery_map_modal');
            this.modal.classList.add('modal', 'fade');
            //language=HTML
            this.modal.innerHTML = `
                <div class="modal-dialog wl-delivery-modal" role="document">
                    <div class="modal-content">
                        <button type="button" class="close" data-dismiss="modal"></button>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="bx-soa-custom-label">Адрес доставки:</label>
                                <input type="text" class="form-control bx-soa-customer-input bx-ios-fix" id="suggestAddress" autocomplete="off">
                            </div>
                            <div id="delivery_map" style="height: 400px"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" id="wl_delivery_map_confirm">Выбрать</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.append(this.modal);
            let suggestAddress = document.getElementById('suggestAddress');
            this.modal.querySelector('#wl_delivery_map_confirm').addEventListener('click', () => {
                document.getElementById('wl_delivery_area_zone').value = this.zone;
                document.getElementById('wl_delivery_area_address').value = suggestAddress.value;
                document.getElementById('wl_delivery_area_address_display').innerText = suggestAddress.value;
                if(this.zone == "other") {
                    alert('Ваш адрес находится в зоне повышенных тарифов курьерских служб, поэтому к стоимости заказа добавлена стоимость доставки');
                }
                BX.Sale.OrderAjaxComponent.sendRequest();
                $(this.modal).modal('hide');
            });
        }
    }
}

