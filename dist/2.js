webpackJsonp([2],{

/***/ 138:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _collapsiblize = __webpack_require__(139);

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

/***/ 139:
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

/***/ })

});