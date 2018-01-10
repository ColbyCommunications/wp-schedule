/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(5);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(2);

var _EventPicker = __webpack_require__(4);

var _EventPicker2 = _interopRequireDefault(_EventPicker);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _asyncToGenerator(fn) { return function () { var gen = fn.apply(this, arguments); return new Promise(function (resolve, reject) { function step(key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { return Promise.resolve(value).then(function (value) { step("next", value); }, function (err) { step("throw", err); }); } } return step("next"); }); }; }

(function () {
  var eventPicker = new _EventPicker2.default({
    checkboxes: document.querySelectorAll('.schedule__tag-list [type="checkbox"]'),
    events: document.querySelectorAll('.schedule [data-event]'),
    resetBox: document.querySelector('.schedule__tag-form [name="all-event-types"]')
  });

  if (eventPicker.shouldRun()) {
    eventPicker.run();
  }
})();

_asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee() {
  var googleMaps;
  return regeneratorRuntime.wrap(function _callee$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          googleMaps = document.querySelectorAll('[data-google-map]');

          console.log('hi!');

          if (googleMaps) {
            _context.next = 4;
            break;
          }

          return _context.abrupt('return');

        case 4:
          _context.next = 6;
          return Promise.reject(function webpackMissingModule() { var e = new Error("Cannot find module \"https://maps.googleapis.com/maps/api/js?key=AIzaSyB0qIhjsPorqev_z2xjTzWIBsix6ZNDK20\""); e.code = 'MODULE_NOT_FOUND';; return e; }());

        case 6:
          console.log(googleMaps);

        case 7:
        case 'end':
          return _context.stop();
      }
    }
  }, _callee, undefined);
}))();

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _collapsiblize = __webpack_require__(3);

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

window.addEventListener('load', function () {
  [].concat(_toConsumableArray(document.querySelectorAll('[data-collapsible]'))).forEach(function (container) {
    var heading = container.querySelector('.collapsible-heading');
    var panel = container.querySelector('.collapsible-panel');

    if (heading && panel) {
      (0, _collapsiblize.collapsiblize)({ heading: heading, panel: panel });
    }
  });
});

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

var removeEmptyParagraphs = function removeEmptyParagraphs(container) {
  [].concat(_toConsumableArray(container.querySelectorAll('p'))).forEach(function (p) {
    if (p.innerHTML.trim().length === 0) {
      container.removeChild(p);
    }
  });
};

var ensureTypeAndPressedAttributes = function ensureTypeAndPressedAttributes(heading) {
  if (!heading.hasAttribute('aria-pressed')) {
    heading.setAttribute('aria-pressed', 'false');
  }

  if (!heading.hasAttribute('type')) {
    heading.setAttribute('type', 'button');
  }
};

var ensureAriaHiddenAttribute = function ensureAriaHiddenAttribute(panel) {
  if (!panel.hasAttribute('aria-hidden')) {
    panel.setAttribute('aria-hidden', 'true');
  }
};

var togglePress = function togglePress(heading) {
  heading.setAttribute('aria-pressed', heading.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
};

var toggle = function toggle(panel) {
  var wasHidden = panel.getAttribute('aria-hidden');
  panel.setAttribute('aria-hidden', wasHidden === 'true' ? 'false' : 'true');

  panel.dispatchEvent(new CustomEvent('change', {
    detail: { open: wasHidden === 'true' }
  }));
};

var collapsiblize = exports.collapsiblize = function collapsiblize(_ref) {
  var heading = _ref.heading,
      panel = _ref.panel;

  removeEmptyParagraphs(panel);
  ensureTypeAndPressedAttributes(heading);
  ensureAriaHiddenAttribute(panel);

  heading.addEventListener('click', function () {
    togglePress(heading);
    toggle(panel);
  });
};

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Filters events based on the checked/unchecked states of taxonomy checkboxes.
 */
var EventPicker = function () {
  function EventPicker(_ref) {
    var checkboxes = _ref.checkboxes,
        events = _ref.events,
        resetBox = _ref.resetBox;

    _classCallCheck(this, EventPicker);

    this.handleCheckBoxChange = this.handleCheckBoxChange.bind(this);
    this.addCheckboxListener = this.addCheckboxListener.bind(this);
    this.maybeToggleEvent = this.maybeToggleEvent.bind(this);

    this.checkboxes = checkboxes;
    this.events = events;
    this.resetBox = resetBox;

    this.activeTags = [];
  }

  _createClass(EventPicker, [{
    key: 'shouldRun',
    value: function shouldRun() {
      return this.checkboxes && this.events;
    }
  }, {
    key: 'run',
    value: function run() {
      [].concat(_toConsumableArray(this.checkboxes)).forEach(this.addCheckboxListener);
      this.addResetBoxListener();
    }
  }, {
    key: 'addCheckboxListener',
    value: function addCheckboxListener(checkbox) {
      checkbox.addEventListener('change', this.handleCheckBoxChange);
    }
  }, {
    key: 'addResetBoxListener',
    value: function addResetBoxListener() {
      var _this = this;

      this.resetBox.addEventListener('change', function (event) {
        _this.resetBox.checked = true;
        _this.activeTags = [];
        _this.checkboxes.forEach(function (checkbox) {
          checkbox.checked = false;
        });
        _this.filterEvents();
      });
    }
  }, {
    key: 'isActive',
    value: function isActive(tag) {
      return this.activeTags.indexOf(tag) !== -1;
    }
  }, {
    key: 'activate',
    value: function activate(tag) {
      this.activeTags.push(tag);
    }
  }, {
    key: 'deactivate',
    value: function deactivate(tag) {
      this.activeTags = this.activeTags.filter(function (activeTag) {
        return activeTag !== tag;
      });
    }
  }, {
    key: 'handleCheckBoxChange',
    value: function handleCheckBoxChange(_ref2) {
      var _ref2$target = _ref2.target,
          target = _ref2$target === undefined ? { checked: checked, value: value } : _ref2$target;

      var tag = value;

      if (checked === true && !isActive(tag)) {
        this.activate(tag);
      } else if (checked === false && isActive(tag)) {
        this.deactivate(tag);
      }

      if (this.activeTags.length) {
        this.resetBox.checked = false;
      }

      this.filterEvents();
    }
  }, {
    key: 'filterEvents',
    value: function filterEvents() {
      [].concat(_toConsumableArray(this.events)).forEach(this.maybeToggleEvent);
    }
  }, {
    key: 'eventShouldShow',
    value: function eventShouldShow(event) {
      // Everything shows when there are no active tags.
      if (!this.activeTags.length) {
        return true;
      }

      // The event is not tagged.
      if (!event.hasAttribute('data-event-tag-ids')) {
        return false;
      }

      // Check whether at least one of the event's tags is active.
      var tagIds = event.getAttribute('data-event-tag-ids').split(',');
      for (var i = 0; i < tagIds.length; i += 1) {
        if (this.activeTags.indexOf(tagIds[i]) !== -1) {
          return true;
        }
      }

      // None of the event's tags are active.
      return false;
    }
  }, {
    key: 'maybeToggleEvent',
    value: function maybeToggleEvent(event) {
      event.style.display = this.eventShouldShow(event) ? 'initial' : 'none';
    }
  }]);

  return EventPicker;
}();

exports.default = EventPicker;

/***/ }),
/* 5 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);