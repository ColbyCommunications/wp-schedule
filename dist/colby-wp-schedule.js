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
module.exports = __webpack_require__(6);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _colbyWpCollapsible = __webpack_require__(2);

var _colbyWpCollapsible2 = _interopRequireDefault(_colbyWpCollapsible);

var _EventPicker = __webpack_require__(4);

var _EventPicker2 = _interopRequireDefault(_EventPicker);

var _GoogleMap = __webpack_require__(5);

var _GoogleMap2 = _interopRequireDefault(_GoogleMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

var initEventPicker = function initEventPicker() {
  var eventPicker = new _EventPicker2.default({
    checkboxes: document.querySelectorAll('.schedule__tag-list [type="checkbox"]'),
    events: document.querySelectorAll('.schedule [data-event]'),
    resetBox: document.querySelector('.schedule__tag-form [name="all-event-types"]')
  });

  if (eventPicker.shouldRun()) {
    eventPicker.run();
  }
};

var initMaps = function initMaps() {
  [].concat(_toConsumableArray(document.querySelectorAll('.collapsible-panel'))).forEach(function (panel) {
    var mapContainer = panel.querySelector('[data-google-map]');

    if (mapContainer) {
      new _GoogleMap2.default({ panel: panel, mapContainer: mapContainer });
    }
  });
};

window.addEventListener('load', _colbyWpCollapsible2.default.init);
window.addEventListener('load', initMaps);
window.addEventListener('load', initEventPicker);

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _collapsiblize = __webpack_require__(3);

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Collapsibles = function () {
  function Collapsibles() {
    _classCallCheck(this, Collapsibles);
  }

  _createClass(Collapsibles, null, [{
    key: 'init',
    value: function init() {
      if (!Collapsibles.hasStarted) {
        Collapsibles.run();
      }
    }
  }, {
    key: 'run',
    value: function run() {
      Collapsibles.hasStarted = true;
      window.addEventListener('load', function () {
        [].concat(_toConsumableArray(document.querySelectorAll('[data-collapsible]'))).forEach(function (container) {
          var heading = container.querySelector('.collapsible-heading');
          var panel = container.querySelector('.collapsible-panel');

          if (heading && panel) {
            (0, _collapsiblize.collapsiblize)({ heading: heading, panel: panel });
          }
        });
      });
    }
  }]);

  return Collapsibles;
}();

exports.default = Collapsibles;

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
  console.log(heading.getAttribute('aria-pressed'));
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
 * Filters visibility of events based on the checked/unchecked states of taxonomy checkboxes.
 */
var EventPicker = function () {
  _createClass(EventPicker, null, [{
    key: 'check',
    value: function check(checkbox) {
      checkbox.checked = true;
    }
  }, {
    key: 'uncheck',
    value: function uncheck(checkbox) {
      checkbox.checked = false;
    }
  }]);

  function EventPicker(_ref) {
    var _this = this;

    var checkboxes = _ref.checkboxes,
        events = _ref.events,
        resetBox = _ref.resetBox;

    _classCallCheck(this, EventPicker);

    this.onCheckBoxChange = this.onCheckBoxChange.bind(this);
    this.onResetBoxChange = this.onResetBoxChange.bind(this);
    this.addCheckboxListener = this.addCheckboxListener.bind(this);
    this.maybeToggleEvent = this.maybeToggleEvent.bind(this);

    this.shouldRun = function () {
      return _this.checkboxes && _this.events;
    };

    this.checkboxes = checkboxes;
    this.events = events;
    this.resetBox = resetBox;
    this.activeTags = [];
  }

  _createClass(EventPicker, [{
    key: 'shouldShow',
    value: function shouldShow(event) {
      // Everything shows when there are no active tags.
      if (!this.activeTags.length) {
        return true;
      }

      // The event is not tagged.
      if (!event.hasAttribute('data-event-tag-ids')) {
        return false;
      }

      // Check whether at least one of the event's tags is active.
      return this.activeTagsIntersect(event.getAttribute('data-event-tag-ids').split(','));
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
      checkbox.addEventListener('change', this.onCheckBoxChange);
    }
  }, {
    key: 'addResetBoxListener',
    value: function addResetBoxListener() {
      this.resetBox.addEventListener('change', this.onResetBoxChange);
    }
  }, {
    key: 'activeTagsInclude',
    value: function activeTagsInclude(tag) {
      return this.activeTags.indexOf(tag) !== -1;
    }
  }, {
    key: 'activeTagsIntersect',
    value: function activeTagsIntersect(eventTags) {
      for (var i = 0; i < eventTags.length; i += 1) {
        if (this.activeTagsInclude(eventTags[i])) {
          return true;
        }
      }

      return false;
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
    key: 'onCheckBoxChange',
    value: function onCheckBoxChange(_ref2) {
      var _ref2$target = _ref2.target,
          checked = _ref2$target.checked,
          tag = _ref2$target.value;

      if (checked && !this.activeTagsInclude(tag)) {
        this.activate(tag);
      } else if (checked === false && this.activeTagsInclude(tag)) {
        this.deactivate(tag);
      }

      if (this.activeTags.length) {
        EventPicker.uncheck(this.resetBox);
      }

      this.showActiveEvents();
    }
  }, {
    key: 'onResetBoxChange',
    value: function onResetBoxChange() {
      EventPicker.check(this.resetBox);
      this.activeTags = [];
      this.checkboxes.forEach(EventPicker.uncheck);
      this.showActiveEvents();
    }
  }, {
    key: 'showActiveEvents',
    value: function showActiveEvents() {
      [].concat(_toConsumableArray(this.events)).forEach(this.maybeToggleEvent);
    }
  }, {
    key: 'maybeToggleEvent',
    value: function maybeToggleEvent(event) {
      event.style.display = this.shouldShow(event) ? 'initial' : 'none';
    }
  }]);

  return EventPicker;
}();

exports.default = EventPicker;

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var GoogleMap = function () {
  function GoogleMap(_ref) {
    var _this = this;

    var panel = _ref.panel,
        mapContainer = _ref.mapContainer;

    _classCallCheck(this, GoogleMap);

    this.started = false;

    this.panel = panel;
    this.mapContainer = mapContainer;

    this.lat = Number(this.mapContainer.getAttribute('data-lat'));
    this.lng = Number(this.mapContainer.getAttribute('data-lng'));
    this.zoom = Number(this.mapContainer.getAttribute('data-zoom'));
    this.address = this.mapContainer.getAttribute('data-address');

    this.panel.addEventListener('change', function (event) {
      if (event.detail.open === true && _this.started === false) {
        _this.started = true;
        _this.initMap();
      }
    });
  }

  _createClass(GoogleMap, [{
    key: 'initMap',
    value: function initMap() {
      var _this2 = this;

      this.map = new google.maps.Map(this.mapContainer, {
        zoom: this.zoom,
        center: new google.maps.LatLng(this.lat, this.lng)
      });

      this.marker = new google.maps.Marker({
        position: {
          lat: this.lat,
          lng: this.lng
        },
        map: this.map
      });

      this.infowindow = new google.maps.InfoWindow({
        content: '<a target=\"_blank\" href="' + ('https://www.google.com/maps/dir/Current+Location/' + this.lat + ',' + this.lng) + '">Get Directions</a>'
      });

      this.marker.addListener('click', function () {
        _this2.infowindow.open(_this2.map, _this2.marker);
      });
    }
  }]);

  return GoogleMap;
}();

exports.default = GoogleMap;

/***/ }),
/* 6 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);