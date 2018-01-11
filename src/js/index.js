import Collapsibles from 'colby-wp-collapsible';

import EventPicker from './EventPicker';
import GoogleMap from './GoogleMap';

const initEventPicker = () => {
  const eventPicker = new EventPicker({
    checkboxes: document.querySelectorAll(
      '.schedule__tag-list [type="checkbox"]'
    ),
    events: document.querySelectorAll('.schedule [data-event]'),
    resetBox: document.querySelector(
      '.schedule__tag-form [name="all-event-types"]'
    ),
  });

  if (eventPicker.shouldRun()) {
    eventPicker.run();
  }
};

const initMaps = () => {
  [...document.querySelectorAll('.collapsible-panel')].forEach(panel => {
    const mapContainer = panel.querySelector('[data-google-map]');

    if (mapContainer) {
      new GoogleMap({ panel, mapContainer });
    }
  });
};

window.addEventListener('load', Collapsibles.init);
window.addEventListener('load', initMaps);
window.addEventListener('load', initEventPicker);
