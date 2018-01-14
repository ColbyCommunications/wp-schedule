import CheckBox from './CheckBox';

/**
 * Filters visibility of events based on the checked/unchecked states
 * of taxonomy checkboxes.
 */
class EventPicker {
  onCheckBoxChange = this.onCheckBoxChange.bind(this);
  addCheckboxListener = this.addCheckboxListener.bind(this);
  maybeToggleEvent = this.maybeToggleEvent.bind(this);

  constructor({ checkboxes, events }) {
    this.checkboxElements = [...checkboxes];
    this.events = events;
  }

  shouldRun = () => this.checkboxElements.length && this.events;

  /**
   * True if the event post should be visible.
   * @param {HTMLDivElement} event
   * @return {bool}
   */
  shouldShow(event) {
    // Everything shows when there are no active tags.
    if (event.getAttribute('data-event-always-visible') === 'true') {
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

    this.activeTags = this.checkboxElements
      .map(element => (element.checked ? element.getAttribute('value') : null))
      .filter(element => element);

    [...this.checkboxes].forEach(this.addCheckboxListener);
    this.showActiveEvents();
  }

  addCheckboxListener(checkbox) {
    checkbox.addEventListener('change', this.onCheckBoxChange);
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
