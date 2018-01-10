/**
 * Filters events based on the checked/unchecked states of taxonomy checkboxes.
 */
class EventPicker {
  handleCheckBoxChange = this.handleCheckBoxChange.bind(this);
  addCheckboxListener = this.addCheckboxListener.bind(this);
  maybeToggleEvent = this.maybeToggleEvent.bind(this);

  constructor({ checkboxes, events, resetBox }) {
    this.checkboxes = checkboxes;
    this.events = events;
    this.resetBox = resetBox;

    this.activeTags = [];
  }

  shouldRun() {
    return this.checkboxes && this.events;
  }

  run() {
    [...this.checkboxes].forEach(this.addCheckboxListener);
    this.addResetBoxListener();
  }

  addCheckboxListener(checkbox) {
    checkbox.addEventListener('change', this.handleCheckBoxChange);
  }

  addResetBoxListener() {
    this.resetBox.addEventListener('change', event => {
      this.resetBox.checked = true;
      this.activeTags = [];
      this.checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      this.filterEvents();
    });
  }

  isActive(tag) {
    return this.activeTags.indexOf(tag) !== -1;
  }

  activate(tag) {
    this.activeTags.push(tag);
  }

  deactivate(tag) {
    this.activeTags = this.activeTags.filter(activeTag => activeTag !== tag);
  }

  handleCheckBoxChange({ target: { checked, value: tag } }) {
    if (checked === true && !this.isActive(tag)) {
      this.activate(tag);
    } else if (checked === false && this.isActive(tag)) {
      this.deactivate(tag);
    }

    if (this.activeTags.length) {
      this.resetBox.checked = false;
    }

    this.filterEvents();
  }

  filterEvents() {
    [...this.events].forEach(this.maybeToggleEvent);
  }

  eventShouldShow(event) {
    // Everything shows when there are no active tags.
    if (!this.activeTags.length) {
      return true;
    }

    // The event is not tagged.
    if (!event.hasAttribute('data-event-tag-ids')) {
      return false;
    }

    // Check whether at least one of the event's tags is active.
    const tagIds = event.getAttribute('data-event-tag-ids').split(',');
    for (var i = 0; i < tagIds.length; i += 1) {
      if (this.activeTags.indexOf(tagIds[i]) !== -1) {
        return true;
      }
    }

    // None of the event's tags are active.
    return false;
  }

  maybeToggleEvent(event) {
    event.style.display = this.eventShouldShow(event) ? 'initial' : 'none';
  }
}

export default EventPicker;
