import GoogleMap from './GoogleMap';

export const initMaps = () => {
  [...document.querySelectorAll('.collapsible-panel')].forEach(panel => {
    const mapContainer = panel.querySelector('[data-google-map]');
    if (mapContainer) {
      new GoogleMap({ panel, mapContainer });
    }
  });
};
