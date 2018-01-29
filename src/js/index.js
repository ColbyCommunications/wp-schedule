import React from 'react';
import ReactDOM from 'react-dom';
import Collapsibles from 'colby-wp-collapsible';
import AddToCalendar from 'react-add-to-calendar';

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
    days: document.querySelectorAll('.schedule .day'),
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

const initAddToCalendar = () => {
  [...document.querySelectorAll('[data-add-to-calendar]')].forEach(
    container => {
      const title = container.getAttribute('data-title');
      const description = container.getAttribute('data-description') || '';
      const location = container.getAttribute('data-location');
      const startTime = container.getAttribute('data-start-time');
      const endTime = container.getAttribute('data-end-time');

      if (title && location && startTime && endTime) {
        ReactDOM.render(
          <AddToCalendar
            event={{ title, description, location, startTime, endTime }}
            buttonLabel="Add to Calendar"
            displayItemIcons={false}
            buttonClassClosed="react-add-to-calendar__button primary btn"
          />,
          container
        );
      }
    }
  );
};

initEventPicker();

window.addEventListener('load', Collapsibles.init);
window.addEventListener('load', initAddToCalendar);
window.addEventListener('load', initMaps);
