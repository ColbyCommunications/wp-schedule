import React from 'react';
import ReactDOM from 'react-dom';
import AddToCalendar from 'react-add-to-calendar';

export const initAddToCalendar = () => {
  [...document.querySelectorAll('[data-add-to-calendar]')].forEach(
    container => {
      const title = container.getAttribute('data-title');
      const description = container.getAttribute('data-description') || '';
      const location = container.getAttribute('data-location');
      const startTime = container.getAttribute('data-start-time');
      const endTime = container.getAttribute('data-end-time');

      if (title && startTime && endTime) {
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
