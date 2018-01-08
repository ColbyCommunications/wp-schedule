import '../css/colby-wp-schedule.css';

class EventPicker {
  handleCheckBoxChange = this.handleCheckBoxChange.bind(this);
  addCheckboxListener = this.addCheckboxListener.bind(this);
  maybeToggleEvent = this.maybeToggleEvent.bind(this);

  constructor({ checkboxes, events }) {
    this.checkboxes = checkboxes;
    this.events = events;

    this.activeTags = [];
  }

  shouldRun() {
    return this.checkboxes && this.events;
  }

  run() {
    [...this.checkboxes].forEach(this.addCheckboxListener);
  }

  addCheckboxListener(checkbox) {
    checkbox.addEventListener('change', this.handleCheckBoxChange);
  }

  handleCheckBoxChange(event) {
    if (
      event.target.checked === true &&
      this.activeTags.indexOf(event.target.value) === -1
    ) {
      this.activeTags.push(event.target.value);
    } else if (
      event.target.checked === false &&
      this.activeTags.indexOf(event.target.value) !== -1
    ) {
      this.activeTags = this.activeTags.filter(
        tag => tag !== event.target.value
      );
    }

    this.filterEvents();
  }

  filterEvents() {
    [...this.events].forEach(this.maybeToggleEvent);
  }

  maybeToggleEvent(event) {
    if (!event.hasAttribute('data-event-tag-ids')) {
      if (this.activeTags.length) {
        event.style.display = 'none';
      } else {
        event.style.display = 'initial';
      }

      return;
    }

    const tagIds = event.getAttribute('data-event-tag-ids').split(',');
    for (var i = 0; i < tagIds.length; i += 1) {
      if (this.activeTags.indexOf(tagIds[i]) !== -1) {
        event.style.display = 'initial';
        return;
      }
    }

    event.style.display = 'none';
  }
}

(() => {
  const eventPicker = new EventPicker({
    checkboxes: document.querySelectorAll(
      '.schedule__tag-list [type="checkbox"]'
    ),
    events: document.querySelectorAll('.schedule [data-event]'),
  });

  if (eventPicker.shouldRun()) {
    eventPicker.run();
  }
})();
