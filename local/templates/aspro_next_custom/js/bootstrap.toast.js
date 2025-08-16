(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery'), require('popper.js')) :
        typeof define === 'function' && define.amd ? define(['exports', 'jquery', 'popper.js'], factory) :
            (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.bootstrap = {}, global.jQuery, global.Popper));
})(this, (function (exports, $, Popper) {
    'use strict';

    function _interopDefaultLegacy(e) { return e && typeof e === 'object' && 'default' in e ? e : { 'default': e }; }

    var $__default = /*#__PURE__*/_interopDefaultLegacy($);
    var Popper__default = /*#__PURE__*/_interopDefaultLegacy(Popper);

    function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }

    function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        Object.defineProperty(Constructor, "prototype", {
            writable: false
        });
        return Constructor;
    }

    function _extends() {
        _extends = Object.assign ? Object.assign.bind() : function (target) {
            for (var i = 1; i < arguments.length; i++) {
                var source = arguments[i];

                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }

            return target;
        };
        return _extends.apply(this, arguments);
    }

    function _inheritsLoose(subClass, superClass) {
        subClass.prototype = Object.create(superClass.prototype);
        subClass.prototype.constructor = subClass;

        _setPrototypeOf(subClass, superClass);
    }

    function _setPrototypeOf(o, p) {
        _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
            o.__proto__ = p;
            return o;
        };
        return _setPrototypeOf(o, p);
    }

    /**
     * --------------------------------------------------------------------------
     * Bootstrap (v4.6.2): util.js
     * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
     * --------------------------------------------------------------------------
     */
    /**
     * Private TransitionEnd Helpers
     */

    var TRANSITION_END = 'transitionend';
    var MAX_UID = 1000000;
    var MILLISECONDS_MULTIPLIER = 1000; // Shoutout AngusCroll (https://goo.gl/pxwQGp)

    function toType(obj) {
        if (obj === null || typeof obj === 'undefined') {
            return "" + obj;
        }

        return {}.toString.call(obj).match(/\s([a-z]+)/i)[1].toLowerCase();
    }

    function getSpecialTransitionEndEvent() {
        return {
            bindType: TRANSITION_END,
            delegateType: TRANSITION_END,
            handle: function handle(event) {
                if ($__default["default"](event.target).is(this)) {
                    return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
                }

                return undefined;
            }
        };
    }

    function transitionEndEmulator(duration) {
        var _this = this;

        var called = false;
        $__default["default"](this).one(Util.TRANSITION_END, function () {
            called = true;
        });
        setTimeout(function () {
            if (!called) {
                Util.triggerTransitionEnd(_this);
            }
        }, duration);
        return this;
    }

    function setTransitionEndSupport() {
        $__default["default"].fn.emulateTransitionEnd = transitionEndEmulator;
        $__default["default"].event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
    }
    /**
     * Public Util API
     */


    var Util = {
        TRANSITION_END: 'bsTransitionEnd',
        getUID: function getUID(prefix) {
            do {
                // eslint-disable-next-line no-bitwise
                prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
            } while (document.getElementById(prefix));

            return prefix;
        },
        getSelectorFromElement: function getSelectorFromElement(element) {
            var selector = element.getAttribute('data-target');

            if (!selector || selector === '#') {
                var hrefAttr = element.getAttribute('href');
                selector = hrefAttr && hrefAttr !== '#' ? hrefAttr.trim() : '';
            }

            try {
                return document.querySelector(selector) ? selector : null;
            } catch (_) {
                return null;
            }
        },
        getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
            if (!element) {
                return 0;
            } // Get transition-duration of the element


            var transitionDuration = $__default["default"](element).css('transition-duration');
            var transitionDelay = $__default["default"](element).css('transition-delay');
            var floatTransitionDuration = parseFloat(transitionDuration);
            var floatTransitionDelay = parseFloat(transitionDelay); // Return 0 if element or transition duration is not found

            if (!floatTransitionDuration && !floatTransitionDelay) {
                return 0;
            } // If multiple durations are defined, take the first


            transitionDuration = transitionDuration.split(',')[0];
            transitionDelay = transitionDelay.split(',')[0];
            return (parseFloat(transitionDuration) + parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
        },
        reflow: function reflow(element) {
            return element.offsetHeight;
        },
        triggerTransitionEnd: function triggerTransitionEnd(element) {
            $__default["default"](element).trigger(TRANSITION_END);
        },
        supportsTransitionEnd: function supportsTransitionEnd() {
            return Boolean(TRANSITION_END);
        },
        isElement: function isElement(obj) {
            return (obj[0] || obj).nodeType;
        },
        typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
            for (var property in configTypes) {
                if (Object.prototype.hasOwnProperty.call(configTypes, property)) {
                    var expectedTypes = configTypes[property];
                    var value = config[property];
                    var valueType = value && Util.isElement(value) ? 'element' : toType(value);

                    if (!new RegExp(expectedTypes).test(valueType)) {
                        throw new Error(componentName.toUpperCase() + ": " + ("Option \"" + property + "\" provided type \"" + valueType + "\" ") + ("but expected type \"" + expectedTypes + "\"."));
                    }
                }
            }
        },
        findShadowRoot: function findShadowRoot(element) {
            if (!document.documentElement.attachShadow) {
                return null;
            } // Can find the shadow root otherwise it'll return the document


            if (typeof element.getRootNode === 'function') {
                var root = element.getRootNode();
                return root instanceof ShadowRoot ? root : null;
            }

            if (element instanceof ShadowRoot) {
                return element;
            } // when we don't find a shadow root


            if (!element.parentNode) {
                return null;
            }

            return Util.findShadowRoot(element.parentNode);
        },
        jQueryDetection: function jQueryDetection() {
            if (typeof $__default["default"] === 'undefined') {
                throw new TypeError('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.');
            }

            var version = $__default["default"].fn.jquery.split(' ')[0].split('.');
            var minMajor = 1;
            var ltMajor = 2;
            var minMinor = 9;
            var minPatch = 1;
            var maxMajor = 4;

            if (version[0] < ltMajor && version[1] < minMinor || version[0] === minMajor && version[1] === minMinor && version[2] < minPatch || version[0] >= maxMajor) {
                throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0');
            }
        }
    };
    Util.jQueryDetection();
    setTransitionEndSupport();

    var NAME = 'toast';
    var VERSION = '4.6.2';
    var DATA_KEY = 'bs.toast';
    var EVENT_KEY = "." + DATA_KEY;
    var JQUERY_NO_CONFLICT = $__default["default"].fn[NAME];
    var CLASS_NAME_FADE = 'fade';
    var CLASS_NAME_HIDE = 'hide';
    var CLASS_NAME_SHOW = 'show';
    var CLASS_NAME_SHOWING = 'showing';
    var EVENT_CLICK_DISMISS = "click.dismiss" + EVENT_KEY;
    var EVENT_HIDE = "hide" + EVENT_KEY;
    var EVENT_HIDDEN = "hidden" + EVENT_KEY;
    var EVENT_SHOW = "show" + EVENT_KEY;
    var EVENT_SHOWN = "shown" + EVENT_KEY;
    var SELECTOR_DATA_DISMISS = '[data-dismiss="toast"]';
    var Default = {
        animation: true,
        autohide: true,
        delay: 500
    };
    var DefaultType = {
        animation: 'boolean',
        autohide: 'boolean',
        delay: 'number'
    };
    /**
     * Class definition
     */

    var Toast = /*#__PURE__*/function () {
        function Toast(element, config) {
            this._element = element;
            this._config = this._getConfig(config);
            this._timeout = null;

            this._setListeners();
        } // Getters


        var _proto = Toast.prototype;

        // Public
        _proto.show = function show() {
            var _this = this;

            var showEvent = $__default["default"].Event(EVENT_SHOW);
            $__default["default"](this._element).trigger(showEvent);

            if (showEvent.isDefaultPrevented()) {
                return;
            }

            this._clearTimeout();

            if (this._config.animation) {
                this._element.classList.add(CLASS_NAME_FADE);
            }

            var complete = function complete() {
                _this._element.classList.remove(CLASS_NAME_SHOWING);

                _this._element.classList.add(CLASS_NAME_SHOW);

                $__default["default"](_this._element).trigger(EVENT_SHOWN);

                if (_this._config.autohide) {
                    _this._timeout = setTimeout(function () {
                        _this.hide();
                    }, _this._config.delay);
                }
            };

            this._element.classList.remove(CLASS_NAME_HIDE);

            Util.reflow(this._element);

            this._element.classList.add(CLASS_NAME_SHOWING);

            if (this._config.animation) {
                var transitionDuration = Util.getTransitionDurationFromElement(this._element);
                $__default["default"](this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
            } else {
                complete();
            }
        };

        _proto.hide = function hide() {
            if (!this._element.classList.contains(CLASS_NAME_SHOW)) {
                return;
            }

            var hideEvent = $__default["default"].Event(EVENT_HIDE);
            $__default["default"](this._element).trigger(hideEvent);

            if (hideEvent.isDefaultPrevented()) {
                return;
            }

            this._close();
        };

        _proto.dispose = function dispose() {
            this._clearTimeout();

            if (this._element.classList.contains(CLASS_NAME_SHOW)) {
                this._element.classList.remove(CLASS_NAME_SHOW);
            }

            $__default["default"](this._element).off(EVENT_CLICK_DISMISS);
            $__default["default"].removeData(this._element, DATA_KEY);
            this._element = null;
            this._config = null;
        } // Private
            ;

        _proto._getConfig = function _getConfig(config) {
            config = _extends({}, Default, $__default["default"](this._element).data(), typeof config === 'object' && config ? config : {});
            Util.typeCheckConfig(NAME, config, this.constructor.DefaultType);
            return config;
        };

        _proto._setListeners = function _setListeners() {
            var _this2 = this;

            $__default["default"](this._element).on(EVENT_CLICK_DISMISS, SELECTOR_DATA_DISMISS, function () {
                return _this2.hide();
            });
        };

        _proto._close = function _close() {
            var _this3 = this;

            var complete = function complete() {
                _this3._element.classList.add(CLASS_NAME_HIDE);

                $__default["default"](_this3._element).trigger(EVENT_HIDDEN);
            };

            this._element.classList.remove(CLASS_NAME_SHOW);

            if (this._config.animation) {
                var transitionDuration = Util.getTransitionDurationFromElement(this._element);
                $__default["default"](this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
            } else {
                complete();
            }
        };

        _proto._clearTimeout = function _clearTimeout() {
            clearTimeout(this._timeout);
            this._timeout = null;
        } // Static
            ;

        Toast._jQueryInterface = function _jQueryInterface(config) {
            return this.each(function () {
                var $element = $__default["default"](this);
                var data = $element.data(DATA_KEY);

                var _config = typeof config === 'object' && config;

                if (!data) {
                    data = new Toast(this, _config);
                    $element.data(DATA_KEY, data);
                }

                if (typeof config === 'string') {
                    if (typeof data[config] === 'undefined') {
                        throw new TypeError("No method named \"" + config + "\"");
                    }

                    data[config](this);
                }
            });
        };

        _createClass(Toast, null, [{
            key: "VERSION",
            get: function get() {
                return VERSION;
            }
        }, {
            key: "DefaultType",
            get: function get() {
                return DefaultType;
            }
        }, {
            key: "Default",
            get: function get() {
                return Default;
            }
        }]);

        return Toast;
    }();
    /**
     * jQuery
     */


    $__default["default"].fn[NAME] = Toast._jQueryInterface;
    $__default["default"].fn[NAME].Constructor = Toast;

    $__default["default"].fn[NAME].noConflict = function () {
        $__default["default"].fn[NAME] = JQUERY_NO_CONFLICT;
        return Toast._jQueryInterface;
    };

    exports.Toast = Toast;
    exports.Util = Util;

    Object.defineProperty(exports, '__esModule', { value: true });

}));