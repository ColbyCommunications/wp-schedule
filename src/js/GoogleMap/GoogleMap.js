class GoogleMap {
  started = false;

  constructor({ panel, mapContainer }) {
    this.panel = panel;
    this.mapContainer = mapContainer;

    this.lat = Number(this.mapContainer.getAttribute('data-lat'));
    this.lng = Number(this.mapContainer.getAttribute('data-lng'));
    this.zoom = Number(this.mapContainer.getAttribute('data-zoom'));

    this.panel.addEventListener('change', event => {
      if (event.detail.open === true && this.started === false) {
        this.started = true;
        this.initMap();
      }
    });
  }

  initMap() {
    this.map = new google.maps.Map(this.mapContainer, {
      zoom: this.zoom,
      center: new google.maps.LatLng(this.lat, this.lng),
    });

    this.marker = new google.maps.Marker({
      position: {
        lat: this.lat,
        lng: this.lng,
      },
      map: this.map,
    });

    this.infowindow = new google.maps.InfoWindow({
      content:
        '<a target="_blank" href="' +
        `https://www.google.com/maps/dir/Current+Location/${this.lat},${
          this.lng
        }` +
        '">Get Directions</a>',
    });

    this.marker.addListener('click', () => {
      this.infowindow.open(this.map, this.marker);
    });
  }
}

export default GoogleMap;
