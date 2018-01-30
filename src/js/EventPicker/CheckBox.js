class Checkbox {
  constructor(element) {
    this.element = element;
  }

  addEventListener(eventTag, callback) {
    this.element.addEventListener(eventTag, callback);
  }

  check() {
    this.element.checked = true;
    this.element.setAttribute('checked', true);
  }

  uncheck() {
    this.element.checked = false;
    this.element.removeAttribute('checked');
  }
}

export default Checkbox;
