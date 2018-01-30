import EventPicker from '.';

export const initEventPicker = () => {
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
