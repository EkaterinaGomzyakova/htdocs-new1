class DeliveryMap {
    map = null;
    inputCoordinates = null;
    coordinates = [];

    constructor() {
        ymaps.ready(() => {
            this.map = this.initMap();
            this.initArea();
        });
    }

    initMap() {
        return new ymaps.Map("ya_map", {
            center: [52.6031, 39.5708], zoom: 10, controls: ['smallMapDefaultSet']
        });
    }

    initArea() {
        this.inputCoordinates = document.querySelector('[name=yandex_coordinates]');

        try {
            this.coordinates = JSON.parse(this.inputCoordinates.value);
        } catch (e) {
        }
        let areaDelivery = new ymaps.Polygon(this.coordinates, {}, {
            editorDrawingCursor: "crosshair", fillColor: 'rgba(141,239,141,0.3)', strokeColor: '#9090fc', strokeWidth: 3
        });

        areaDelivery.events.add(['geometrychange'], e => {
            let coordinates = areaDelivery.geometry.getCoordinates();
            this.inputCoordinates.value = JSON.stringify(coordinates);
        });

        this.map.geoObjects.add(areaDelivery);
        areaDelivery.editor.startDrawing();
    }
}

new DeliveryMap();

