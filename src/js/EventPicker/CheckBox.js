class Checkbox {
  constructor(element) {
    this.element = element;
  }

  addEventListener(eventTag, callback) {
    this.element.addEventListener(eventTag, callback);
  }

  check() {
    this.element.checked = true;
  }

  uncheck() {
    this.element.checked = false;
  }
}

export default Checkbox;
