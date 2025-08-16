document.addEventListener('DOMContentLoaded',  function (){
    new AdminDelivery();
});

class AdminDelivery {
    constructor() {
        this.address = null;
        this.popup = null;
        this.coordinates = null;
        this.deliveryZone = null;
        this.deliveryPoint = null;
        this.propertiesID = {};
        this.zone = 'other';
        this.orderID = 0;
        if (BX.Sale.Admin.OrderEditPage.orderId) {
            this.orderID = BX.Sale.Admin.OrderEditPage.orderId;
        }
        this.selectedDeliveryID = null;
        this.containerID = null;
        BX.addCustomEvent('onAjaxSuccess', this.initDeliveryMApHandler.bind(this));
        this.initDeliveryMApHandler();
    }

    initDeliveryMApHandler() {
        let container = document.querySelector('[id^="shipment_container_"]');
        if (!container) {
            return;
        }
        this.containerID = parseInt(container.getAttribute('id').replace('shipment_container_', ''));
         

        if(!container.querySelector('#DELIVERY_' + this.containerID)) {
            return;
        } 
        
        let selectedDeliveryID = container.querySelector('#DELIVERY_' + this.containerID).value;

        if (selectedDeliveryID != this.selectedDeliveryID) {
            this.selectedDeliveryID = selectedDeliveryID;
            let sectionMap = container.querySelector('#section_map_' + this.containerID);
            BX.ajax.runAction('wl:delivery_area.api.delivery.getMap',
                {
                    data: {
                        order_id: this.orderID,
                        delivery_id: this.selectedDeliveryID
                    }
                }).then(response => {
                if (response.data.status) {
                    this.coordinates = response.data.coordinates;

                    this.propertiesID = response.data.properties_id;
                    this.zone = response.data.zone;
                    this.link = container.querySelector('#select_delivery_address');
                    if (!this.link) {
                        let deliveryInfo = document.createElement('div');
                        deliveryInfo.classList.add('delivery-info');
                        //language=HTML
                        deliveryInfo.innerHTML = `
                            <input type="button" class="adm-btn-save delivery-info__btn" id="select_delivery_address" value="Указать адрес доставки">
                            <div class="delivery-info__address" id="delivery_info_wrap" style="display: none">
                                <span class="delivery-info__label">Адрес: </span>
                                <span class="delivery-info__value" id="delivery_address"></span>
                            </div>
                        `

                        this.link = deliveryInfo.querySelector('#select_delivery_address');
                        sectionMap.append(deliveryInfo);
                        this.initModal();
                        this.link.addEventListener('click', (event) => {
                            event.preventDefault();
                            this.popup.show();
                        })
                    }
                    this.setAddress(response.data.address);

                }
            })
        }
    }

    setAddress(address = '') {
        this.address = address;
        if (address != null && address.length > 0) {
            document.querySelector('#delivery_info_wrap').style.display = 'block';
            document.querySelector('#delivery_address').innerText = address;
        }
    }

    initModal() {
        //language=HTML
        let content = `
            <div class="adm-workarea">
                <div class="adm-detail-content-cell-r tal">
                    <label class="bx-soa-custom-label">Адрес доставки:</label>
                    <input type="text" class="adm-bus-input" id="suggestAddress" autocomplete="off">
                </div>
                <div class="adm-detail-content-cell-r tal">
                    <div id="delivery_map" style="height: 400px"></div>
                </div>
            </div>
        `
        this.popup = BX.PopupWindowManager.create("wl-delivery-popup", null, {
            content: content,
            width: 700, // ширина окна
            // height: 700, // высота окна
            zIndex: 1300, // z-index
            closeIcon: {
                opacity: 1
            },
            titleBar: 'Выбор адреса доставки',
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: false, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: true, // можно ресайзить
            min_height: 300, // минимальная высота окна
            min_width: 300, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: true, // появится уголок
            overlay: {
                // объект со стилями фона
                // backgroundColor: 'black',
                // opacity: 500
            },
            buttons: [
                new BX.PopupWindowButton({
                    text: "Выбрать",
                    className: "popup-window-button-accept",
                    events: {
                        click: () => {
                            this.updateOrder();
                        }
                    }
                }),
            ],
            events: {
                onPopupShow: () => {
                    this.loadMap();
                },
            }
        });
    }

    loadMap() {
        if (this.map == null) {
            document.getElementById('suggestAddress').value = this.address;
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

            let suggestView = new ymaps.SuggestView('suggestAddress', {
                boundedBy: [[52.645174, 39.512531], [52.554689, 39.671234]],
            });
            suggestView.events.add('select', event => {
                let suggestSelectedItem = event.get('item');
                this.setAddress(suggestSelectedItem.value);
                this.drawPlacemarkFromAddress(suggestSelectedItem.value);
            });
            if (this.address) {
                this.drawPlacemarkFromAddress(this.address);
            }
        }
    }

    updateOrder() {
        if (this.orderID > 0) {
            BX.ajax.runAction('wl:delivery_area.api.delivery.updateOrder',
                {
                    data: {
                        order_id: this.orderID,
                        delivery_id: this.selectedDeliveryID,
                        address: this.address,
                        zone: this.zone
                    }
                }).then(response => {
                {
                    document.querySelector('#UPDATE_DELIVERY_INFO_' + this.containerID).click();
                }
            })
        } else {
            if (this.propertiesID['WL_DELIVERY_ADDRESS']) {
                document.querySelector('[name="PROPERTIES[' + this.propertiesID['WL_DELIVERY_ADDRESS'] + ']"]').value = this.address;
            }

            if (this.propertiesID['DELIVERY_AREA']) {
                document.querySelector('[name="PROPERTIES[' + this.propertiesID['DELIVERY_AREA'] + ']"]').value = this.zone;
            }
            document.querySelector('#UPDATE_DELIVERY_INFO_' + this.containerID).click();
        }
        this.popup.close();
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
}