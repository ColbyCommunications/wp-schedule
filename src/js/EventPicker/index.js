import CheckBox from './CheckBox';

/**
 * Filters visibility of events based on the checked/unchecked states
 * of taxonomy checkboxes.
 */
class EventPicker {
  onCheckBoxChange = this.onCheckBoxChange.bind(this);
  onResetBoxChange = this.onResetBoxChange.bind(this);
  addCheckboxListener = this.addCheckboxListener.bind(this);
  maybeToggleEvent = this.maybeToggleEvent.bind(this);

  constructor({ checkboxes, events, resetBox }) {
    this.checkboxElements = [...checkboxes];
    this.resetBoxElement = resetBox;
    this.events = events;
  }

  shouldRun = () =>
    this.checkboxElements.length && this.events && this.resetBoxElement;

  shouldShow(event) {
    // Everything shows when there are no active tags.
    if (!this.activeTags.length) {
      return true;
    }

    // The event is not tagged.
    if (!event.hasAttribute('data-event-tag-ids')) {
      return false;
    }

    // Check whether at least one of the event's tags is active.
    return this.activeTagsIntersect(
      event.getAttribute('data-event-tag-ids').split(',')
    );
  }

  run() {
    this.checkboxes = this.checkboxElements.map(
      checkbox => new CheckBox(checkbox)
    );
    this.resetBox = new CheckBox(this.resetBoxElement);
    this.activeTags = this.checkboxElements
      .map(element => (element.checked ? element.getAttribute('value') : null))
      .filter(element => element);

    [...this.checkboxes].forEach(this.addCheckboxListener);
    this.addResetBoxListener();
    this.showActiveEvents();
  }

  addCheckboxListener(checkbox) {
    checkbox.addEventListener('change', this.onCheckBoxChange);
  }

  addResetBoxListener() {
    this.resetBox.addEventListener('change', this.onResetBoxChange);
  }

  activeTagsInclude = tag => this.activeTags.indexOf(tag) !== -1;

  activeTagsIntersect(eventTags) {
    for (var i = 0; i < eventTags.length; i += 1) {
      if (this.activeTagsInclude(eventTags[i])) {
        return true;
      }
    }

    return false;
  }

  activate(tag) {
    this.activeTags.push(tag);
  }

  deactivate(tag) {
    this.activeTags = this.activeTags.filter(activeTag => activeTag !== tag);
  }

  onCheckBoxChange({ target: { checked, value: tag } }) {
    if (checked) {
      this.activate(tag);
    } else {
      this.deactivate(tag);
    }

    if (this.activeTags.length) {
      this.resetBox.uncheck();
    } else {
      this.resetBox.check();
    }

    this.showActiveEvents();
  }

  onResetBoxChange() {
    this.resetBox.check();
    this.activeTags = [];
    this.checkboxes.forEach(checkbox => {
      checkbox.uncheck();
    });
    this.showActiveEvents();
  }

  showActiveEvents() {
    [...this.events].forEach(this.maybeToggleEvent);
  }

  maybeToggleEvent(event) {
    event.style.display = this.shouldShow(event) ? 'initial' : 'none';
  }
}

export default EventPicker;
