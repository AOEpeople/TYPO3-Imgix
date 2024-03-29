/*! http://www.imgix.com imgix.js - v2.1.0 - 2015-11-30
 _                    _             _
 (_)                  (_)           (_)
 _  _ __ ___    __ _  _ __  __      _  ___
 | || '_ ` _ \  / _` || |\ \/ /     | |/ __|
 | || | | | | || (_| || | >  <  _   | |\__ \
 |_||_| |_| |_| \__, ||_|/_/\_\(_)  | ||___/
 __/ |             _/ |
 |___/             |__/

 */

(function() {

// THIS FILE WAS GENERATED. DO NOT EDIT DIRECTLY. They will be overwritten
// Edit files in /src and then run: grunt build
// Please use the minified version of this file in production.


    // Define and run polyfills first. Don't want/need these? grunt build --no-polyfills
    function initPolyfills() {

        // Object.freeze polyfill (pure pass through)
        if (!Object.freeze) {
            Object.freeze = function freeze(object) {
                return object;
            };
        }

        // filter polyfill: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/filter
        if (!Array.prototype.filter) {
            Array.prototype.filter = function(fun/*, thisArg*/) {
                if (this === void 0 || this === null) {
                    throw new TypeError();
                }

                var t = Object(this);
                var len = t.length >>> 0;
                if (typeof fun !== 'function') {
                    throw new TypeError();
                }

                var res = [];
                var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
                for (var i = 0; i < len; i++) {
                    if (i in t) {
                        var val = t[i];
                        if (fun.call(thisArg, val, i, t)) {
                            res.push(val);
                        }
                    }
                }

                return res;
            };
        }

        // map polyfill: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/map
        if (!Array.prototype.map) {
            Array.prototype.map = function(callback, thisArg) {

                var T, A, k;

                if (this == null) {
                    throw new TypeError(" this is null or not defined");
                }

                var O = Object(this);

                var len = O.length >>> 0;

                if (typeof callback !== "function") {
                    throw new TypeError(callback + " is not a function");
                }

                if (arguments.length > 1) {
                    T = thisArg;
                }

                A = new Array(len);
                k = 0;
                while (k < len) {
                    var kValue, mappedValue;
                    if (k in O) {
                        kValue = O[k];
                        mappedValue = callback.call(T, kValue, k, O);
                        A[k] = mappedValue;
                    }
                    k++;
                }

                return A;
            };
        }

        // Polyfill or document.querySelectorAll + document.querySelector
        // should only matter for the poor souls on IE <= 8
        // https://gist.github.com/chrisjlee/8960575
        if (!document.querySelectorAll) {
            document.querySelectorAll = function (selectors) {
                var style = document.createElement('style'), elements = [], element;
                document.documentElement.firstChild.appendChild(style);
                document._qsa = [];

                style.styleSheet.cssText = selectors + '{x-qsa:expression(document._qsa && document._qsa.push(this))}';
                window.scrollBy(0, 0);
                style.parentNode.removeChild(style);

                while (document._qsa.length) {
                    element = document._qsa.shift();
                    element.style.removeAttribute('x-qsa');
                    elements.push(element);
                }
                document._qsa = null;
                return elements;
            };
        }

        if (!document.querySelector) {
            document.querySelector = function (selectors) {
                var elements = document.querySelectorAll(selectors);
                return (elements.length) ? elements[0] : null;
            };
        }

        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function (needle) {
                for (var i = 0; i < this.length; i++) {
                    if (this[i] === needle) {
                        return i;
                    }
                }
                return -1;
            };
        }

        if (!Array.isArray) {
            Array.isArray = function(arg) {
                return Object.prototype.toString.call(arg) === '[object Array]';
            };
        }

        if (!Object.keys) {
            Object.keys = (function() {

                var hasOwnProperty = Object.prototype.hasOwnProperty,
                    hasDontEnumBug = !({ toString: null }).propertyIsEnumerable('toString'),
                    dontEnums = [
                        'toString',
                        'toLocaleString',
                        'valueOf',
                        'hasOwnProperty',
                        'isPrototypeOf',
                        'propertyIsEnumerable',
                        'constructor'
                    ],
                    dontEnumsLength = dontEnums.length;

                return function(obj) {
                    if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
                        throw new TypeError('Object.keys called on non-object');
                    }

                    var result = [], prop, i;

                    for (prop in obj) {
                        if (hasOwnProperty.call(obj, prop)) {
                            result.push(prop);
                        }
                    }

                    if (hasDontEnumBug) {
                        for (i = 0; i < dontEnumsLength; i++) {
                            if (hasOwnProperty.call(obj, dontEnums[i])) {
                                result.push(dontEnums[i]);
                            }
                        }
                    }
                    return result;
                };
            }());
        }

        // polyfill for getComputedStyle (main for ie8)
        !('getComputedStyle' in window) && (window.getComputedStyle = (function () {
            function getPixelSize(element, style, property, fontSize) {
                var
                    sizeWithSuffix = style[property],
                    size = parseFloat(sizeWithSuffix),
                    suffix = sizeWithSuffix.split(/\d/)[0],
                    rootSize;

                fontSize = fontSize != null ? fontSize : /%|em/.test(suffix) && element.parentElement ? getPixelSize(element.parentElement, element.parentElement.currentStyle, 'fontSize', null) : 16;
                rootSize = property == 'fontSize' ? fontSize : /width/i.test(property) ? element.clientWidth : element.clientHeight;

                return (suffix == 'em') ? size * fontSize : (suffix == 'in') ? size * 96 : (suffix == 'pt') ? size * 96 / 72 : (suffix == '%') ? size / 100 * rootSize : size;
            }

            function setShortStyleProperty(style, property) {
                var
                    borderSuffix = property == 'border' ? 'Width' : '',
                    t = property + 'Top' + borderSuffix,
                    r = property + 'Right' + borderSuffix,
                    b = property + 'Bottom' + borderSuffix,
                    l = property + 'Left' + borderSuffix;

                style[property] = (style[t] == style[r] == style[b] == style[l] ? [style[t]]
                    : style[t] == style[b] && style[l] == style[r] ? [style[t], style[r]]
                    : style[l] == style[r] ? [style[t], style[r], style[b]]
                    : [style[t], style[r], style[b], style[l]]).join(' ');
            }

            function CSSStyleDeclaration(element) {
                var
                    currentStyle = element.currentStyle,
                    style = this,
                    fontSize = getPixelSize(element, currentStyle, 'fontSize', null);

                for (property in currentStyle) {
                    if (/width|height|margin.|padding.|border.+W/.test(property) && style[property] !== 'auto') {
                        style[property] = getPixelSize(element, currentStyle, property, fontSize) + 'px';
                    } else if (property === 'styleFloat') {
                        style['float'] = currentStyle[property];
                    } else {
                        style[property] = currentStyle[property];
                    }
                }

                setShortStyleProperty(style, 'margin');
                setShortStyleProperty(style, 'padding');
                setShortStyleProperty(style, 'border');

                style.fontSize = fontSize + 'px';

                return style;
            }

            CSSStyleDeclaration.prototype = {
                constructor: CSSStyleDeclaration,
                getPropertyPriority: function () {},
                getPropertyValue: function ( prop ) {
                    return this[prop] || '';
                },
                item: function () {},
                removeProperty: function () {},
                setProperty: function () {},
                getPropertyCSSValue: function () {}
            };

            function getComputedStyle(element) {
                return new CSSStyleDeclaration(element);
            }

            return getComputedStyle;
        })(window));
    }

    if (typeof window !== 'undefined') {
        initPolyfills();
    }

    'use strict';

// Establish the root object, `window` in the browser, or `exports` on the server.
    var root = this;

    /**
     * `imgix` is the root namespace for all imgix client code.
     * @namespace imgix
     */
    var imgix = {
        version: '2.1.0'
    };

// expose imgix to browser or node
    if (typeof exports !== 'undefined') {
        if (typeof module !== 'undefined' && module.exports) {
            exports = module.exports = imgix;
        }
        exports.imgix = imgix;
    } else {
        root.imgix = imgix;
    }

    var IMGIX_USABLE_CLASS = 'imgix-usable';

    /**
     * Reports if an element is an image tag
     * @memberof imgix
     * @static
     * @param {Element} el the element to check
     * @returns {boolean} true if the element is an img tag
     */
    imgix.isImageElement = function (el) {
        return (el && el.tagName && el.tagName.toLowerCase() === 'img');
    };

    /**
     * Intelligently sets an image on an element after the image has been cached.
     * @memberof imgix
     * @static
     * @param {Element} el the element to place the image on
     * @param {string} url the url of the image to set
     * @param {function} callback called once image has been preloaded and set
     */
    imgix.setElementImageAfterLoad = function (el, imgUrl, callback) {
        var img = new Image();
        img.onload = function () {
            imgix.setElementImage(el, imgUrl);
            if (typeof callback === 'function') {
                callback(el, imgUrl);
            }
        };
        if (el.hasAttribute('crossorigin')) {
            img.setAttribute('crossorigin', el.getAttribute('crossorigin'));
        }

        img.src = imgUrl;
    };

    /**
     * Intelligently sets an image on an element.
     * @memberof imgix
     * @static
     * @param {Element} el the element to check
     * @param {string} url the url of the image to set
     * @returns {boolean} true on success
     */
    imgix.setElementImage = function (el, imgUrl) {
        if (!el) {
            return false;
        }

        if (imgix.isImageElement(el)) {
            if (el.src !== imgUrl) {
                el.src = imgUrl;
            }
            return true;
        } else {
            var curBg = imgix.getBackgroundImage(el);
            if (curBg !== imgUrl) {
                if (curBg) {
                    el.style.cssText = el.style.cssText.replace(curBg, imgUrl);
                    return true;
                } else {
                    if (document.addEventListener) {
                        el.style.backgroundImage = 'url(' + imgUrl + ')';
                    } else {
                        el.style.cssText = 'background-image:url(' + imgUrl + ')';
                    }
                    return true;
                }
            }
        }

        return false;
    };

    /**
     * An empty 1x1 transparent image
     * @memberof imgix
     * @static
     * @returns {string} url of an empty image
     */
    imgix.getEmptyImage = function () {
        return imgix.versionifyUrl('https://assets.imgix.net/pixel.gif');
    };

    /**
     * Intelligently returns the image on the element
     * @memberof imgix
     * @static
     * @param {Element} el the element to check
     * @returns {string} url of the image on the element
     */
    imgix.getElementImage = function (el) {
        if (imgix.isImageElement(el)) {
            return el.src;
        } else {
            return imgix.getBackgroundImage(el);
        }
    };

    /**
     * Returns the background image for an element
     * @memberof imgix
     * @static
     * @param {Element} el the element to check
     * @returns {string} url of the image on the element
     */
    imgix.getBackgroundImage = function (el) {
        // This regex comes from http://stackoverflow.com/a/20054738/506330
        var regex = /\burl\s*\(\s*["']?([^"'\r\n,]+)["']?\s*\)/gi,
            style,
            matches;

        if (window.getComputedStyle) {
            style = window.getComputedStyle(el);
        } else if (document.documentElement.currentStyle) {
            style = el.currentStyle;
        }

        if (!style || !style.backgroundImage) {
            style = el.style;
        }

        matches = regex.exec(style.backgroundImage);

        if (matches && matches.length > 1) {
            return matches[1];
        } else {
            return '';
        }
    };

    /**
     * Gives all elements on the page that have images (or could img). Does NOT support IE8
     * @memberof imgix
     * @static
     * @returns {NodeList} html elements with images
     */
    imgix.getElementsWithImages = function () {
        imgix.markElementsWithImages();

        return document.querySelectorAll('.' + IMGIX_USABLE_CLASS);
    };

    /**
     * Does an element have an image attached
     * @memberof imgix
     * @static
     * @param {Element} el element to check for images
     * @returns {boolean} true if passed element has an image
     */
    imgix.hasImage = function (el) {
        var toCheck = el.style.cssText ? el.style.cssText.toLowerCase() : el.style.cssText;
        return el && (imgix.isImageElement(el) || toCheck.indexOf('background-image') !== -1);
    };

    /**
     * Helper method that attaches IMGIX_CLASS to all elements with images on a page
     * @memberof imgix
     * @private
     * @static
     */
    imgix.markElementsWithImages = function () {
        var all = document.getElementsByTagName('*');
        for (var i = 0, max = all.length; i < max; i++) {
            if (imgix.hasImage(all[i])) {
                imgix.setImgixClass(all[i]);
            }
        }
    };

    /**
     * Checks if an element has a class applied (via jquery)
     * @memberof imgix
     * @static
     * @param {Element} elem element to check for class
     * @param {string} name class name to look for
     * @returns {boolean} true if element has the class
     */
    imgix.hasClass = function (elem, name) {
        return (' ' + elem.className + ' ').indexOf(' ' + name + ' ') > -1;
    };

    /**
     * Helper method that 'marks' an element as 'imgix usable' by adding special classes
     * @memberof imgix
     * @static
     * @private
     * @param {Element} el the element to place the class on
     * @returns {string} auto-generated class name (via xpath)
     */
    imgix.setImgixClass = function (el) {
        if (imgix.hasClass(el, IMGIX_USABLE_CLASS)) {
            return imgix.getImgixClass(el);
        }

        var cls = imgix.getXPathClass(imgix.getElementTreeXPath(el));

        el.classList.add(cls);
        el.classList.add(IMGIX_USABLE_CLASS);

        return imgix.getImgixClass(el);
    };

    /**
     * Helper method that returns generated (via xpath) class name for 'marked' image elements
     * @memberof imgix
     * @static
     * @private
     * @param {Element} el the element to get the class for
     * @returns {string} class name
     */
    imgix.getImgixClass = function (el) {
        if (imgix.hasClass(el, IMGIX_USABLE_CLASS)) {
            return el.className.match(/imgix-el-[^\s]+/)[0];
        }
    };

    imgix.getXPathClass = function (xpath) {
        var suffix;

        if (xpath){
            suffix = imgix.hashCode(xpath);
        } else {
            suffix = (new Date()).getTime().toString(36);
        }

        return 'imgix-el-' + suffix;
    };

    imgix.getElementImageSize = function (el) {
        var w = 0,
            h = 0;

        if (imgix.isImageElement(el)) {
            w = el.naturalWidth;
            h = el.naturalHeight;
        } else {
            w = imgix.helpers.extractInt(imgix.getCssProperty(el, 'width'));
            h = imgix.helpers.extractInt(imgix.getCssProperty(el, 'height'));
        }

        return {
            width: w,
            height: h
        };
    };

    imgix.getCssPropertyById = function (elmId, property) {
        var elem = document.getElementById(elmId);
        return imgix.helpers.getElementCssProperty(elem, property);
    };

    imgix.getCssProperty = function (el, property) {
        return imgix.helpers.getElementCssProperty(el, property);
    };

    imgix.getCssPropertyBySelector = function (sel, property) {
        var elem = document.querySelector(sel);
        return imgix.helpers.getElementCssProperty(elem, property);
    };

    imgix.instanceOfImgixURL = function (x) {
        return x && x.toString() === '[object imgixURL]';
    };

    imgix.setGradientOnElement = function (el, colors, baseColor) {
        var baseColors = [];
        if (typeof baseColor === 'undefined') {
            // transparent base colors if not set
            baseColors = ['transparent', 'transparent'];
        } else {
            var base = imgix.hexToRGB(baseColor); // force rgb if in hex
            if (base.slice(0, 4) === 'rgba') {
                // if given an rgba then use that as the upper and force 0 for lower
                baseColors.push(base);
                baseColors.push(imgix.applyAlphaToRGB(base, 0));
            } else {
                // default to 0 to 50% transparency for passed solid base color
                baseColors.push(imgix.applyAlphaToRGB(base, 0.5));
                baseColors.push(imgix.applyAlphaToRGB(base, 0));
            }
        }

        var backgroundGradients = [
            '-ms-linear-gradient(top, ' + baseColors[0] + ' 0%, ' + baseColors[1] + ' 100%),-ms-linear-gradient(bottom left, ' + colors[2] + ' 0%,' + colors[4] + ' 25%, ' + colors[6] + ' 50%, ' + colors[8] + ' 75%,' + colors[10] + ' 100%)',
            '-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, ' + baseColors[1] + '), color-stop(100%, ' + baseColors[0] + ')),-webkit-gradient(linear, 0% 100%, 100% 0%, color-stop(0%, ' + colors[2] + '), color-stop(25%, ' + colors[4] + '), color-stop(50%, ' + colors[6] + '), color-stop(75%, ' + colors[7] + '), color-stop(100%, ' + colors[10] + '))',
            '-webkit-linear-gradient(top, ' + baseColors[0] + ', ' + baseColors[1] + ' 100%),-webkit-linear-gradient(bottom left, ' + colors[2] + ', ' + colors[4] + ', ' + colors[6] + ',' + colors[8] + ')',
            '-moz-linear-gradient(top, ' + baseColors[0] + ', ' + baseColors[1] + ' ),-moz-linear-gradient(bottom left, ' + colors[2] + ', ' + colors[4] + ', ' + colors[6] + ',' + colors[8] + ')',
            '-o-linear-gradient(top, ' + baseColors[0] + ',' + baseColors[1] + '),-o-linear-gradient(bottom left, ' + colors[2] + ', ' + colors[4] + ', ' + colors[6] + ',' + colors[8] + ')',
            'linear-gradient(top, ' + baseColors[0] + ',' + baseColors[1] + '),linear-gradient(bottom left, ' + colors[2] + ', ' + colors[4] + ', ' + colors[6] + ',' + colors[8] + ')'
        ];

        for (var x = 0; x < backgroundGradients.length; x++) {
            el.style.backgroundImage = backgroundGradients[x];
        }
    };

    imgix.isDef = function (obj) {
        return (typeof obj !== 'undefined');
    };

// Adapted from http://stackoverflow.com/a/22429679
    imgix.hashCode = function (str) {
        /*jshint bitwise:false */
        var i, l, hval = 0x811c9dc5;
        for (i = 0, l = str.length; i < l; i++) {
            hval ^= str.charCodeAt(i);
            hval += (hval << 1) + (hval << 4) + (hval << 7) + (hval << 8) + (hval << 24);
        }

        return ("0000000" + (hval >>> 0).toString(16)).substr(-8);
    };

// DOCS BELOW ARE AUTO GENERATED. DO NOT EDIT BY HAND


    'use strict';

    /**
     * The helper namespace for lower-level functions
     * @namespace imgix.helpers
     */
    imgix.helpers = {
        debouncer: function (func, wait) {
            var timeoutRef;
            return function () {
                var self = this,
                    args = arguments,
                    later = function () {
                        timeoutRef = null;
                        func.apply(self, args);
                    };

                window.clearTimeout(timeoutRef);
                timeoutRef = window.setTimeout(later, wait);
            };
        },

        throttler: function (func, wait) {
            var timeoutRef;
            return function () {
                var self = this,
                    args = arguments,
                    later;

                if (!timeoutRef) {
                    later = function () {
                        timeoutRef = null;
                        func.apply(self, args);
                    };

                    timeoutRef = window.setTimeout(later, wait);
                }
            };
        },

        // FROM: https://github.com/websanova/js-url | unknown license.
        urlParser: (function () {
            function isNumeric(arg) {
                return !isNaN(parseFloat(arg)) && isFinite(arg);
            }

            return function (arg, url) {
                var _ls = url || window.location.toString();

                if (!arg) {
                    return _ls;
                } else {
                    arg = arg.toString();
                }

                if (_ls.substring(0, 2) === '//') {
                    _ls = 'http:' + _ls;
                } else if (_ls.split('://').length === 1) {
                    _ls = 'http://' + _ls;
                }

                url = _ls.split('/');
                var _l = {auth: ''},
                    host = url[2].split('@');

                if (host.length === 1) {
                    host = host[0].split(':');
                } else {
                    _l.auth = host[0]; host = host[1].split(':');
                }

                _l.protocol = url[0];
                _l.hostname = host[0];
                _l.port = (host[1] || ((_l.protocol.split(':')[0].toLowerCase() === 'https') ? '443' : '80'));
                _l.pathname = ( (url.length > 3 ? '/' : '') + url.slice(3, url.length).join('/').split('?')[0].split('#')[0]);
                var _p = _l.pathname;

                if (_p.charAt(_p.length - 1) === '/') {
                    _p = _p.substring(0, _p.length - 1);
                }
                var _h = _l.hostname,
                    _hs = _h.split('.'),
                    _ps = _p.split('/');

                if (arg === 'hostname') {
                    return _h;
                } else if (arg === 'domain') {
                    if (/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/.test(_h)) {
                        return _h;
                    } else {
                        return _hs.slice(-2).join('.');
                    }
                } else if (arg === 'sub') {
                    return _hs.slice(0, _hs.length - 2).join('.');
                } else if (arg === 'port') {
                    return _l.port;
                } else if (arg === 'protocol') {
                    return _l.protocol.split(':')[0];
                } else if (arg === 'auth') {
                    return _l.auth;
                } else if (arg === 'user') {
                    return _l.auth.split(':')[0];
                } else if (arg === 'pass') {
                    return _l.auth.split(':')[1] || '';
                } else if (arg === 'path') {
                    return _l.pathname;
                } else if (arg.charAt(0) === '.') {
                    arg = arg.substring(1);
                    if (isNumeric(arg)) {
                        arg = parseInt(arg, 10);
                        return _hs[arg < 0 ? _hs.length + arg : arg - 1] || '';
                    }
                } else if (isNumeric(arg)) {
                    arg = parseInt(arg, 10);
                    return _ps[arg < 0 ? _ps.length + arg : arg] || '';
                } else if (arg === 'file') {
                    return _ps.slice(-1)[0];
                } else if (arg === 'filename') {
                    return _ps.slice(-1)[0].split('.')[0];
                } else if (arg === 'fileext') {
                    return _ps.slice(-1)[0].split('.')[1] || '';
                } else if (arg.charAt(0) === '?' || arg.charAt(0) === '#') {
                    var params = _ls,
                        param = null;

                    if (arg.charAt(0) === '?') {
                        params = (params.split('?')[1] || '').split('#')[0];
                    } else if (arg.charAt(0) === '#') {
                        params = (params.split('#')[1] || '');
                    }

                    if (!arg.charAt(1)) {
                        return params;
                    }

                    arg = arg.substring(1);
                    params = params.split('&');

                    for (var i = 0, ii = params.length; i < ii; i++) {
                        param = params[i].split('=');
                        if (param[0] === arg) {
                            return param[1] || '';
                        }
                    }

                    return null;
                }

                return '';
            };
        })(),

        mergeObject: function () {
            var obj = {},
                i = 0,
                il = arguments.length,
                key;
            for (; i < il; i++) {
                for (key in arguments[i]) {
                    if (arguments[i].hasOwnProperty(key)) {
                        obj[key] = arguments[i][key];
                    }
                }
            }
            return obj;
        },

        pixelRound: function (pixelSize, pixelStep) {
            return Math.ceil(pixelSize / pixelStep) * pixelStep;
        },

        isNumber: function (value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },

        getWindowDPR: function () {
            var dpr = window.devicePixelRatio ? window.devicePixelRatio : 1;

            if (dpr % 1 !== 0) {
                var tmpStr = dpr.toString();
                tmpStr = tmpStr.split('.')[1];
                dpr = (tmpStr.length > 1 && tmpStr.slice(1, 2) !== '0') ? dpr.toFixed(2) : dpr.toFixed(1);
            }

            return dpr;
        },

        getWindowWidth: function () {
            return Math.max(document.documentElement.clientWidth, window.innerWidth || 0) || 1024;
        },

        getWindowHeight: function () {
            return Math.max(document.documentElement.clientHeight, window.innerHeight || 0) || 768;
        },

        getImgSrc: function (elem) {
            return elem.getAttribute('data-src') || elem.getAttribute('src');
        },

        calculateElementSize: function (elem) {
            var val = {
                width: elem.offsetWidth,
                height: elem.offsetHeight
            };

            if (elem.parentNode === null || elem === document.body) {
                val.width = this.getWindowWidth();
                val.height = this.getWindowHeight();
                return val;
            }

            if (val.width !== 0 || val.height !== 0) {
                if (elem.alt && !elem.fluid) {
                    elem.fluid = true;
                    return this.calculateElementSize(elem.parentNode);
                }
                return val;
            } else {
                var found,
                    prop,
                    past = {},
                    visProp = {position: 'absolute', visibility: 'hidden', display: 'block'};

                for (prop in visProp) {
                    if (visProp.hasOwnProperty(prop)) {
                        past[prop] = elem.style[prop];
                        elem.style[prop] = visProp[prop];
                    }
                }

                found = {
                    width: elem.offsetWidth,
                    height: elem.offsetHeight
                };

                for (prop in visProp) {
                    if (visProp.hasOwnProperty(prop)) {
                        elem.style[prop] = past[prop];
                    }
                }

                if (found.width === 0 || found.height === 0) {
                    return this.calculateElementSize(elem.parentNode);
                } else {
                    return found;
                }
            }
        },

        isReallyObject: function (elem) {
            return elem && typeof elem === 'object' && (elem.toString()) === '[object Object]';
        },

        isFluidSet: function (elem) {
            return elem && typeof elem === 'object' && (elem.toString()) === '[object FluidSet]';
        },

        extractInt: function (str) {
            if (str === undefined) {
                return 0;
            } else if (typeof str === 'number') {
                return str;
            }
            return parseInt(str.replace(/\D/g, ''), 10) || 0;
        },

        camelize: function (str) {
            return str.replace(/[-_\s]+(.)?/g, function (match, c) { return c ? c.toUpperCase() : ''; });
        },

        getElementCssProperty: function (elem, prop) {
            if (window.getComputedStyle) {
                return window.getComputedStyle(elem, null).getPropertyValue(prop);
            } else {
                if (elem && elem.style && prop) {
                    return elem.style[this.camelize(prop)];
                }
            }

            return '';
        },

        matchesSelector: function (elem, selector) {
            var children = (elem.parentNode || document).querySelectorAll(selector);
            return Array.prototype.slice.call(children).indexOf(elem) > -1;
        },

        warn: function(message) {
            if (window.console) {
                window.console.warn(message);
            }
        }
    };

    'use strict';

    /**
     * Represents an imgix url
     * @memberof imgix
     * @constructor
     * @param {string} url An imgix url to start with (optional)
     * @param {object} imgParams imgix query string params (optional)
     */
    imgix.URL = function (url, imgParams) {

        this._autoUpdateSel = null;
        this._autoUpdateCallback = null;

        if (url && url.slice(0, 2) === '//' && window && window.location) {
            url = window.location.protocol + url;
        }

        this.setUrl(url);

        if (typeof imgParams === 'object') {
            this.setParams(imgParams);
        }

        this.paramAliases = {};
    };

    /**
     * Attach a gradient of colors from the imgix image URL to the passed html element (or selector for that element)
     * @memberof imgix
     * @param {string} elemOrSel html elment or css selector for the element
     * @param {string} baseColor color in rgb or hex
     */
    imgix.URL.prototype.attachGradientTo = function (elemOrSel, baseColor, callback) {
        this.getColors(16, function (colors) {
            if (colors && colors.length < 9) {
                imgix.helpers.warn('not enough colors to create a gradient');
                if (callback && typeof callback === 'function') {
                    callback(false);
                }
                return;
            }
            if (typeof elemOrSel === 'string') {
                var results = document.querySelectorAll(elemOrSel);
                if (results && results.length > 0) {
                    for (var i = 0; i < results.length; i++) {
                        imgix.setGradientOnElement(results[i], colors, baseColor);
                    }
                }
            } else {
                imgix.setGradientOnElement(elemOrSel, colors, baseColor);
            }

            if (callback && typeof callback === 'function') {
                callback(true);
            }
        });
    };

    /**
     * Attach the image url (.getUrl() value) to the passed html element (or selector for that element)
     * @memberof imgix
     * @param {string} elemOrSel html elment or css selector for the element
     * @param {function} callback optional callback to be called when image is set on the element
     */
    imgix.URL.prototype.attachImageTo = function (elemOrSel, callback) {
        if (typeof elemOrSel === 'string') {
            var results = document.querySelectorAll(elemOrSel);
            if (results && results.length > 0) {
                for (var i = 0; i < results.length; i++) {
                    imgix.setElementImageAfterLoad(results[i], this.getUrl(), callback);
                }
            }
        } else {
            imgix.setElementImageAfterLoad(elemOrSel, this.getUrl(), callback);
        }
    };

    imgix.createParamString = function () {
        return new imgix.URL('');
    };

    imgix.updateVersion = {};

    var cssColorCache = {};
    /**
     * Get an array of the colors in the image
     * @memberof imgix
     * @param {number} num Desired number of colors
     * @param {colorsCallback} callback handles the response of colors
     */
    imgix.URL.prototype.getColors = function (num, callback) {
        var DEFAULT_COLOR_COUNT = 10,
            paletteUrlObj = new imgix.URL(this.getUrl()),
            jsonUrl;

        function processPaletteData(data) {
            var colors = [],
                i,
                rgb;

            if (!data || !data.colors) {
                return undefined;
            }

            for (i = 0; i < data.colors.length; i++) {
                rgb = [
                    Math.round(data.colors[i].red * 255),
                    Math.round(data.colors[i].green * 255),
                    Math.round(data.colors[i].blue * 255),
                ];

                colors.push('rgb(' + rgb.join(', ') + ')');
            }

            return colors;
        }

        function requestPaletteData() {
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                var data;

                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        data = JSON.parse(xhr.response);
                    } else {
                        data = {
                            colors: [{
                                red: 1,
                                green: 1,
                                blue: 1
                            }]
                        };
                    }

                    cssColorCache[jsonUrl] = processPaletteData(data);

                    callback(cssColorCache[jsonUrl]);
                }
            };

            xhr.open('get', jsonUrl, true);
            xhr.send();
        }

        // Wrangle the arguments, if only one is provided
        if (typeof num === 'function') {
            if (typeof callback === 'number') {
                var tmpNum = callback;
                callback = num;
                num = tmpNum;
            } else {
                callback = num;
                num = DEFAULT_COLOR_COUNT;
            }
        }

        // Set parameters, then get a URL for an AJAX request
        paletteUrlObj.setParams({
            palette: 'json',
            colors: num
        });
        jsonUrl = paletteUrlObj.getUrl();

        // Return the cached colors, if available
        if (cssColorCache.hasOwnProperty(jsonUrl)) {
            if (callback) {
                callback(cssColorCache[jsonUrl]);
            }

            // If no cache is available, do it the hard way
        } else {
            requestPaletteData();
        }
    };
    /**
     * This callback receives the colors in the image.
     * @callback colorsCallback
     * @param {array} colors an array of colors
     */

    imgix.URL.prototype._handleAutoUpdate = function () {
        var self = this,
            totalImages = 0,
            loadedImages = 0,
            curSel = this._autoUpdateSel,
            imgToEls = {};

        if (!imgix.isDef(imgix.updateVersion[curSel])) {
            imgix.updateVersion[curSel] = 1;
        } else {
            imgix.updateVersion[curSel]++;
        }


        function isVersionFresh(v) {
            return curSel === self._autoUpdateSel && v === imgix.updateVersion[curSel];
        }

        function setImage(el, imgUrl) {
            if (!(imgUrl in imgToEls)) {
                imgToEls[imgUrl] = [];
                (function () {
                    var img = new Image(),
                        curV = imgix.updateVersion[curSel],
                        startTime = (new Date()).getTime();

                    img.onload = img.onerror = function () {
                        if (!isVersionFresh(curV)) {
                            return;
                        }

                        for (var i = 0; i < imgToEls[imgUrl].length; i++) {
                            imgix.setElementImage(imgToEls[imgUrl][i], imgUrl);
                            loadedImages++;

                            if (typeof self._autoUpdateCallback === 'function') {
                                var obj = {
                                    element: imgToEls[imgUrl][i],
                                    isComplete: loadedImages === totalImages, // boolean
                                    percentComplete: (loadedImages / totalImages) * 100, // float
                                    totalComplete: loadedImages, // int
                                    loadTime: (new Date()).getTime() - startTime,
                                    total: totalImages // int
                                };

                                self._autoUpdateCallback(obj);
                            }
                        }
                    };
                    if (el.hasAttribute('crossorigin')) {
                        img.setAttribute('crossorigin', el.getAttribute('crossorigin'));
                    }

                    img.src = imgUrl;
                })();
            }

            imgToEls[imgUrl].push(el);
        }

        function applyImg(el) {
            var elImg = imgix.getElementImage(el),
                elBaseUrl = elImg;

            if (elImg && elImg.indexOf('?') !== -1) {
                elBaseUrl = elImg.split('?')[0];
            }

            if (self.getBaseUrl()) {
                setImage(el, self.getUrl());
            } else if (elBaseUrl && self.getQueryString()) {
                setImage(el, elBaseUrl + '?' + self.getQueryString());
            } else {
                loadedImages++;
            }
        }

        if (this._autoUpdateSel !== null) {
            var el = document.querySelectorAll(this._autoUpdateSel);

            totalImages = el.length;

            if (el && totalImages === 1) {
                applyImg(el[0]);
            } else {
                for (var i = 0; i < totalImages; i++) {
                    applyImg(el[i]);
                }
            }
        }
    };


    /**
     * When/if the url changes it will auto re-set the image on the element of the css selector passed
     * @memberof imgix
     * @param {string} sel css selector for an <img> element on the page
     * @param {autoUpdateElementCallback} callback fires whenever the img element is updated
     */
    imgix.URL.prototype.autoUpdateImg = function (sel, callback) {
        this._autoUpdateSel = sel;
        this._autoUpdateCallback = callback;
        this._handleAutoUpdate();
    };
    /**
     *
     * @callback autoUpdateElementCallback
     * @param {object} obj information about element and image
     * @todo how to doc the complex object that is passed back
     */

    imgix.URL.prototype.setUrl = function (url) {
        if (!url || typeof url !== 'string' || url.length === 0) {
            url = imgix.getEmptyImage();
        }
        this.urlParts = imgix.parseUrl(url);
    };

    imgix.URL.prototype.setURL = function (url) {
        return this.setUrl(url);
    };

    imgix.URL.prototype.getURL = function () {
        return this.getUrl();
    };

    imgix.URL.prototype.toString = function () {
        return '[object imgixURL]';
    };

    /**
     * The generated imgix image url
     * @memberof imgix
     * @returns {string} the generated url
     */
    imgix.URL.prototype.getUrl = function () {
        var url = imgix.buildUrl(this.urlParts);

        if (!url || url.length === 0) {
            return imgix.getEmptyImage();
        }

        return url;
    };

    /**
     * Remove an imgix param
     * @memberof imgix
     * @param {string} param the imgix param to remove (e.g. txtfont)
     */
    imgix.URL.prototype.removeParam = function (param) {
        if (this.urlParts.paramValues.hasOwnProperty(param)) {
            delete this.urlParts.paramValues[param];
            this.urlParts.params = Object.keys(this.urlParts.paramValues);
        }
    };

    /**
     * Remove an imgix param then immediately set new params. This only triggers one update if used with autoUpdateImg.
     * @memberof imgix
     * @param {object} params object of params to set
     */
    imgix.URL.prototype.clearThenSetParams = function (params) {
        this.clearParams(false); // do not trigger update yet
        this.setParams(params);
    };

    /**
     * Clear all imgix params attached to the image
     * @memberof imgix
     * @param {boolean} runUpdate (optional) iff using autoUpdateImg should callback be called (defaults to true)
     */
    imgix.URL.prototype.clearParams = function (runUpdate) {
        runUpdate = !imgix.isDef(runUpdate) ? true : runUpdate;

        for (var k in this.urlParts.paramValues) {
            if (this.urlParts.paramValues.hasOwnProperty(k)) {
                this.removeParam(k);
            }
        }

        if (runUpdate) {
            this._handleAutoUpdate();
        }
    };


    /**
     * Set multiple params using using an object (e.g. {txt: 'hello', txtclr: 'f00'})
     * @memberof imgix
     * @param {object} dict an object of imgix params and their values
     * @param {boolean} doOverride should the value(s) be overridden if they already exist (defaults to true)
     */
    imgix.URL.prototype.setParams = function (dict, doOverride) {
        if (imgix.instanceOfImgixURL(dict)) {
            imgix.helpers.warn('setParams warning: dictionary of imgix params expectd. imgix URL instance passed instead');
            return;
        }
        for (var k in dict) {
            if (dict.hasOwnProperty(k)) {
                this.setParam(k, dict[k], doOverride, true);
            }
        }

        this._handleAutoUpdate();
    };

    /**
     * Set a single imgix param value
     * @memberof imgix

     * @param {string} param the imgix param to set (e.g. txtclr)
     * @param {string} value the value to set for the param
     * @param {boolean} doOverride (optional) should the value(s) be overridden if they already exist (defaults to true)
     * @param {boolean} noUpdate (optional) iff using autoUpdateImg should callback be called (defaults to false)
     */
    imgix.URL.prototype.setParam = function (param, value, doOverride, noUpdate) {
        param = param.toLowerCase();

        doOverride = !imgix.isDef(doOverride) ? true : doOverride;
        noUpdate = !imgix.isDef(noUpdate) ? false : noUpdate;

        if (param === 'col' || param === 'colorize' || param === 'blend' || param === 'mono' || param === 'monochrome') {
            if (value.slice(0, 3) === 'rgb') {
                value = imgix.rgbToHex(value);
            }
        }

        if (!doOverride && this.urlParts.paramValues[param]) {
            // we are not overriding because they didn't want to
            return this;
        }

        if (!imgix.isDef(value) || value === null || value.length === 0) {
            this.removeParam(param);
            return this;
        }

        if (this.urlParts.params.indexOf(param) === -1) {
            this.urlParts.params.push(param);
        }

        if (decodeURIComponent(value) === value) {
            value = encodeURIComponent(value);
        }


        this.urlParts.paramValues[param] = String(value);

        if (!noUpdate) {
            this._handleAutoUpdate();
        }

        return this;
    };

    /**
     * Get the value of an imgix param in the query string
     * @memberof imgix
     * @param {string} param the imgix param that you want the value of (e.g. txtclr)
     * @returns {string} the value of the param in the current url
     */
    imgix.URL.prototype.getParam = function (param) {
        if (param === 'mark' || param === 'mask') {
            var result = this.urlParts.paramValues[param];
            // if encoded then decode...
            if (decodeURIComponent(result) !== result) {
                return decodeURIComponent(result);
            }

            return result;
        }
        return this.urlParts.paramValues[param];
    };

    /**
     * Get an object of all the params and their values on the current image
     * @memberof imgix
     * @returns {object} an object of params and their values (e.g. {txt: 'hello', txtclr: 'f00'})
     */
    imgix.URL.prototype.getParams = function () {
        if (this.urlParts.paramValues) {
            return this.urlParts.paramValues;
        }

        return {};
    };

    /**
     * Get the base url. This is getUrl() without the query string
     * @memberof imgix
     * @returns {string} the base url
     */
    imgix.URL.prototype.getBaseUrl = function () {
        var url = this.getUrl();
        if (url.indexOf('?') !== -1) {
            url = this.getUrl().split('?')[0];
        }

        return url !== window.location.href ? url : '';
    };

    /**
     * Get the query string only. This is getUrl() with ONLY the query string (e.g. ?txt=hello&txtclr=f00)
     * @memberof imgix
     * @returns {string} the query string for the url
     */
    imgix.URL.prototype.getQueryString = function () {
        var url = this.getUrl();
        if (url.indexOf('?') !== -1) {
            return this.getUrl().split('?')[1];
        }

        return '';
    };

    imgix.parseUrl = function (url) {
        var
            pkeys = ['protocol', 'hostname', 'port', 'path', '?', '#', 'hostname'],
            keys = ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash', 'host'],
            result = {};

        // create clean object to return
        for (var i = 0; i < keys.length; i++) {
            result[keys[i]] = imgix.helpers.urlParser(pkeys[i], url);
        }

        var qs = result.search;

        result.paramValues = {};
        result.params = [];
        result.baseUrl = url.split('?')[0];

        // parse query string into dictionary
        if (qs && qs.length > 0) {
            if (qs[0] === '?') {
                qs = qs.substr(1, qs.length);
            }

            var parts = qs.split('&');
            for (var y = 0; y < parts.length; y++) {
                var tmp = parts[y].split('=');
                if (tmp[0] && tmp[0].length && tmp[0] !== 's') {
                    result.paramValues[tmp[0]] = (tmp.length === 2 ? tmp[1] : '');
                    if (result.params.indexOf(tmp[0]) === -1) {
                        result.params.push(tmp[0]);
                    }
                }
            }
        }

        return result;
    };

    imgix.buildUrl = function (parsed) {
        var result = parsed.protocol + '://' + parsed.host;

        if (parsed.port !== null && parsed.port !== '80' && parsed.port !== '443') {
            result += ':' + parsed.port;
        }
        result += parsed.pathname;

        // Add version string to this URL
        imgix.versionifyUrl(parsed);

        if (parsed.params.length > 0) {
            parsed.params = parsed.params.map(function (e) {
                return e.toLowerCase();
            });

            // unique only
            parsed.params = parsed.params.filter(function (value, index, self) {
                return self.indexOf(value) === index;
            });

            // sort
            parsed.params = parsed.params.sort(function (a, b) {
                if (a < b) {
                    return -1;
                } else if (a > b) {
                    return 1;
                } else {
                    return 0;
                }
            });

            var qs = [];
            for (var i = 0; i < parsed.params.length; i++) {
                if (parsed.paramValues[parsed.params[i]].length > 0) {
                    qs.push(parsed.params[i] + '=' + parsed.paramValues[parsed.params[i]]);
                }
            }

            if (result.indexOf('?') !== -1) {
                result = result.split('?')[0];
            }

            result += '?' + qs.join('&');
        }

        return result;
    };

    imgix.versionifyUrl = function (target) {
        var versionParam = 'ixjsv',
            splitUrl,
            result;

        if (typeof target === 'string') {
            return imgix.versionifyStringUrl(target);
        } else {
            return imgix.versionifyParsedUrl(target);
        }
    };

    imgix.versionifyStringUrl = function (url) {
        var versionParam = 'ixjsv',
            splitUrl,
            result;

        splitUrl = url.split('?');
        result = splitUrl[0] + '?' + versionParam + '=' + imgix.version;

        if (!!splitUrl[1]) {
            result += '&' + splitUrl[1];
        }

        return result;
    }

    imgix.versionifyParsedUrl = function (parsed) {
        var versionParam = 'ixjsv';

        if (!parsed.paramValues[versionParam]) {
            parsed.params.push(versionParam);
        }
        parsed.paramValues[versionParam] = imgix.version;

        return parsed;
    };

    'use strict';

    var fluidDefaults = {
        fluidClass: 'imgix-fluid',
        updateOnResize: true,
        updateOnResizeDown: false,
        updateOnPinchZoom: false,
        highDPRAutoScaleQuality: true,
        onChangeParamOverride: null,
        autoInsertCSSBestPractices: false,

        fitImgTagToContainerWidth: true,
        fitImgTagToContainerHeight: false,
        ignoreDPR: false,
        pixelStep: 10,
        debounce: 200,
        lazyLoad: false,
        lazyLoadColor: null,
        lazyLoadOffsetVertical: 20,
        lazyLoadOffsetHorizontal: 20,
        throttle: 200,
        maxHeight: 5000,
        maxWidth: 5000,
        onLoad: null
    };

    function getFluidDefaults() {
        return fluidDefaults;
    }

    imgix.FluidSet = function (options) {
        if (imgix.helpers.isReallyObject(options)) {
            this.options = imgix.helpers.mergeObject(getFluidDefaults(), options);
        } else {
            this.options = imgix.helpers.mergeObject(getFluidDefaults(), {});
        }

        this.lazyLoadOffsets = {
            t: Math.max(this.options.lazyLoadOffsetVertical, 0),
            b: Math.max(this.options.lazyLoadOffsetVertical, 0),
            l: Math.max(this.options.lazyLoadOffsetHorizontal, 0),
            r: Math.max(this.options.lazyLoadOffsetHorizontal, 0)
        };

        this.namespace = Math.random().toString(36).substring(7);

        this.windowResizeEventBound = false;
        this.windowScrollEventBound = false;
        this.windowLastWidth = 0;
        this.windowLastHeight = 0;
    };

    imgix.FluidSet.prototype.updateSrc = function (elem, pinchScale) {
        // An empty src attribute throws off the 'hidden' check below,
        // so we need to give it something to actually fill it up
        if (elem.hasAttribute('src') && elem.getAttribute('src') === '') {
            elem.setAttribute('src', imgix.getEmptyImage());
        }

        // Short-circuit if the image is hidden
        if (!elem.offsetWidth && !elem.offsetHeight && !elem.getClientRects().length) {
            return;
        }

        var details = this.getImgDetails(elem, pinchScale || 1),
            newUrl = details.url,
            currentElemWidth = details.width,
            currentElemHeight = details.height;

        if (this.options.lazyLoad) {
            var r = elem.getBoundingClientRect(),
                view = {
                    left: 0 - this.lazyLoadOffsets.l,
                    top: 0 - this.lazyLoadOffsets.t,
                    bottom: (window.innerHeight || document.documentElement.clientHeight) + this.lazyLoadOffsets.b,
                    right: (window.innerWidth || document.documentElement.clientWidth) + this.lazyLoadOffsets.r
                };

            if ((r.top > view.bottom) || (r.left > view.right) || (r.top + currentElemHeight < view.top) || (r.left + currentElemWidth < view.left)) {

                if (!elem.fluidLazyColored && this.options.lazyLoadColor) {
                    elem.fluidLazyColored = 1;
                    var self = this,
                        llcType = typeof this.options.lazyLoadColor,
                        i = new imgix.URL(imgix.helpers.getImgSrc(elem));

                    i.getColors(16, function (colors) {
                        if (!colors) {
                            imgix.helpers.warn('No colors found for', i.getURL(), 'for element', elem);
                            return;
                        }

                        var useColor = null;
                        if (llcType === 'boolean') {
                            useColor = colors[0];
                        } else if (llcType === 'number' && self.options.lazyLoadColor < colors.length) {
                            useColor = colors[self.options.lazyLoadColor];
                        } else if (llcType === 'function') {
                            useColor = self.options.lazyLoadColor(elem, colors);
                        }

                        if (useColor !== null) {
                            if (imgix.isImageElement(elem) && elem.parentNode && elem.parentNode.tagName.toLowerCase() !== 'body') {
                                elem.parentNode.style.backgroundColor = useColor;
                            } else {
                                elem.style.backgroundColor = useColor;
                            }
                        }
                    });
                }
                return;
            }
        }


        elem.lastWidth = elem.lastWidth || 0;
        elem.lastHeight = elem.lastHeight || 0;

        if (this.options.updateOnResizeDown === false && elem.lastWidth >= currentElemWidth && elem.lastHeight >= currentElemHeight) {
            return;
        }


        if (!elem.fluidUpdateCount) {
            elem.fluidUpdateCount = 0;
        }

        var onLoad = function () {};

        if (this.options.onLoad && typeof this.options.onLoad === 'function') {
            onLoad = this.options.onLoad;
        }

        // wrapped onLoad to handle race condition where multiple images are requested before the first one can load
        var wrappedOnLoad = function (el, imgUrl) {
            el.fluidUpdateCount = parseInt(el.fluidUpdateCount, 10) + 1;
            onLoad(el, imgUrl);
        };

        imgix.setElementImageAfterLoad(elem, newUrl, wrappedOnLoad);
        elem.lastWidth = currentElemWidth;
        elem.lastHeight = currentElemHeight;
    };

    imgix.FluidSet.prototype.getImgDetails = function (elem, zoomMultiplier) {
        if (!elem) {
            return;
        }

        var dpr = imgix.helpers.getWindowDPR(),
            pixelStep = this.options.pixelStep,
            elemSize = imgix.helpers.calculateElementSize(!imgix.isImageElement(elem) ? elem.parentNode : elem),
            elemWidth = imgix.helpers.pixelRound(elemSize.width * zoomMultiplier, pixelStep),
            elemHeight = imgix.helpers.pixelRound(elemSize.height * zoomMultiplier, pixelStep);

        if (!elem.url) {
            elem.url = new imgix.URL(imgix.helpers.getImgSrc(elem));
        }

        elem.url.setParams({
            h: '',
            w: ''
        });

        elemWidth = Math.min(elemWidth, this.options.maxWidth);
        elemHeight = Math.min(elemHeight, this.options.maxHeight);

        if (dpr !== 1 && !this.options.ignoreDPR) {
            elem.url.setParam('dpr', dpr);
        }

        if (this.options.highDPRAutoScaleQuality && dpr > 1) {
            elem.url.setParam('q', Math.min(Math.max(parseInt((100 / dpr), 10), 30), 75));
        }

        if (this.options.fitImgTagToContainerHeight && this.options.fitImgTagToContainerWidth) {
            elem.url.setParam('fit', 'crop');
        }

        if (elem.url.getParam('fit') === 'crop') {
            if (elemHeight > 0 && (!imgix.isImageElement(elem) || (imgix.isImageElement(elem) && this.options.fitImgTagToContainerHeight))) {
                elem.url.setParam('h', elemHeight);
            }

            if (elemWidth > 0 && (!imgix.isImageElement(elem) || (imgix.isImageElement(elem) && this.options.fitImgTagToContainerWidth))) {
                elem.url.setParam('w', elemWidth);
            }
        } else {
            if (elemHeight <= elemWidth) {
                elem.url.setParam('w', elemWidth);
            } else {
                elem.url.setParam('h', elemHeight);
            }
        }

        if (!imgix.isImageElement(elem) && this.options.autoInsertCSSBestPractices && elem.style) {
            elem.style.backgroundRepeat = 'no-repeat';
            elem.style.backgroundSize = 'cover';
            elem.style.backgroundPosition = '50% 50%';
        }

        var overrides = {};
        if (this.options.onChangeParamOverride !== null && typeof this.options.onChangeParamOverride === 'function') {
            overrides = this.options.onChangeParamOverride(elemWidth, elemHeight, elem.url.getParams(), elem);
        }

        for (var k in overrides) {
            if (overrides.hasOwnProperty(k)) {
                elem.url.setParam(k, overrides[k]);
            }
        }

        return {
            url: elem.url.getURL(),
            width: elemWidth,
            height: elemHeight
        };
    };

    imgix.FluidSet.prototype.toString = function () {
        return '[object FluidSet]';
    };

    imgix.FluidSet.prototype.reload = function () {
        imgix.fluid(this);

        this.windowLastWidth = imgix.helpers.getWindowWidth();
        this.windowLastHeight = imgix.helpers.getWindowHeight();
    };

    imgix.FluidSet.prototype.attachGestureEvent = function (elem) {
        var self = this;
        if (elem.addEventListener && !elem.listenerAttached) {
            elem.addEventListener('gestureend', function (e) {
                self.updateSrc(this, e.scale);
            }, false);

            elem.addEventListener('gesturechange', function () {
                self.updateSrc(this);
            }, false);

            elem.listenerAttached = true;
        }
    };

    var scrollInstances = {},
        resizeInstances = {};

    imgix.FluidSet.prototype.attachScrollListener = function () {
        var th = this;

        scrollInstances[this.namespace] = imgix.helpers.throttler(function () {
            th.reload();
        }, this.options.throttle);

        if (document.addEventListener) {
            window.addEventListener('scroll', scrollInstances[this.namespace], false);
        } else {
            window.attachEvent('onscroll', scrollInstances[this.namespace]);
        }

        this.windowScrollEventBound = true;
    };

    imgix.FluidSet.prototype.attachWindowResizer = function () {
        var th = this;

        resizeInstances[this.namespace] = imgix.helpers.debouncer(function () {
            if (this.windowLastWidth !== imgix.helpers.getWindowWidth() || this.windowLastHeight !== imgix.helpers.getWindowHeight()) {
                th.reload();
            }
        }, this.options.debounce);

        if (window.addEventListener) {
            window.addEventListener('resize', resizeInstances[this.namespace], false);
        } else if (window.attachEvent) {
            window.attachEvent('onresize', resizeInstances[this.namespace]);
        }

        this.windowResizeEventBound = true;
    };


    /**
     * Enables fluid (responsive) images for any element(s) with the 'imgix-fluid' class.
     * To scope to images within a specific DOM node, pass the enclosing HTML element as the first argument.


     #####Option Descriptions

     `fluidClass` __string__ all elements with this class will have responsive images<br>

     `updateOnResize` __boolean__ should it request a new bigger image when container grows<br>

     `updateOnResizeDown` __boolean__ should it request a new smaller image when container shrinks<br>

     `updateOnPinchZoom` __boolean__ should it request a new image when pinching on a mobile
     device<br>

     `highDPRAutoScaleQuality` __boolean__ should it automatically use a lower quality image on high DPR devices. This is usually nearly undetectable by a human, but offers a significant decrease in file size.<br>

     `onChangeParamOverride` __function__ if defined the following are passed (__number__ h, __number__ w, __object__ params, __HTMLElement__ element). When an object of params is returned they are applied to the image<br>

     `autoInsertCSSBestPractices` __boolean__ should it automatically add `backgroundRepeat = 'no-repeat`; `elem.style.backgroundSize = 'cover'` `elem.style.backgroundPosition = '50% 50%'` to elements with a background image<br>

     `fitImgTagToContainerWidth` __boolean__ should it fit img tag elements to their container's width. Does not apply to background images.<br>

     `fitImgTagToContainerHeight` __boolean__ should it fit img tag elements to their container's height. Does not apply to background images.<br>

     `pixelStep` __number__ image dimensions are rounded to this (e.g. for 10 the value 333 would be rounded to 340)<br>

     `ignoreDPR` __boolean__ when true the `dpr` param is not set on the image.<br>

     `debounce` __number__ postpones resize execution until after this many milliseconds have elapsed since the last time it was invoked.<br>

     `lazyLoad` __boolean__ when true the image is not actually loaded until it is viewable (or within the offset)<br>

     `lazyLoadOffsetVertical` __number__ when `lazyLoad` is true this allows you to set how far above and below the viewport (in pixels) you want before imgix.js starts to load the images.<br>

     `lazyLoadOffsetHorizontal` __number__ when `lazyLoad` is true this allows you to set how far to the left and right of the viewport (in pixels) you want before imgix.js starts to load the images.<br>

     `lazyLoadColor` __boolean__ or __number__ or __function__ When defined the image container's background is set to a color in the image. When value is `true` use first color in the color array, when value is a `number` use that index from the color array, when value is a `function` it uses whatever color is returned by the function (`HTMLElement' el, `Array` colors)

     `throttle` __number__ ensures scroll events fire only once every n milliseconds, throttling lazyLoad activity.<br>

     `maxWidth` __number__ Never set the width parameter higher than this value.<br>

     `maxHeight` __number__ Never set the height parameter higher than this value.<br>

     `onLoad` __function__ Called when an image is loaded. It's passed the `HTMLElement` that contains the image that was just loaded and the URL of that image (`HTMLElement' el, `String` imageURL)<br>

     <b>Default values</b> (passed config will extend these values)

     {
       fluidClass: 'imgix-fluid',
       updateOnResize: true,
       updateOnResizeDown: false,
       updateOnPinchZoom: false,
       highDPRAutoScaleQuality: true,
       onChangeParamOverride: null,
       autoInsertCSSBestPractices: false,
       fitImgTagToContainerWidth: true,
       fitImgTagToContainerHeight: false,
       pixelStep: 10,
       debounce: 200,
       ignoreDPR: false,
       lazyLoad: false,
       lazyLoadOffsetVertical: 20,
       lazyLoadOffsetHorizontal: 20,
       throttle: 200,
       maxWidth: 5000,
       maxHeight: 5000,
       onLoad: null
     }


     * @memberof imgix
     * @static
     * @param [rootNode=document] optional HTML element to scope operations on
     * @param {object} config options for fluid (this extends the defaults)
     */
    imgix.fluid = function () {
        var elem, node;
        if (arguments.length > 0 && arguments[0].nodeType === 1) {
            node = arguments[0];
            elem = arguments[1];
        } else {
            elem = arguments[0];
        }

        if (elem === null) {
            return;
        }

        var options,
            fluidSet;

        if (imgix.helpers.isReallyObject(elem)) {

            var passedKeys = Object.keys(elem),
                goodKeys = Object.keys(getFluidDefaults());

            for (var i = 0; i < passedKeys.length; i++) {
                if (goodKeys.indexOf(passedKeys[i]) === -1) {
                    imgix.helpers.warn('\'' + passedKeys[i] + '\' is not a valid imgix.fluid config option. See https://github.com/imgix/imgix.js/blob/main/docs/api.md#imgix.fluid for a list of valid options.');
                }
            }

            options = imgix.helpers.mergeObject(getFluidDefaults(), elem);
            fluidSet = new imgix.FluidSet(options);
            elem = null;

        } else if (imgix.helpers.isFluidSet(elem)) {
            fluidSet = elem;
            options = fluidSet.options;
        } else {
            options = imgix.helpers.mergeObject(getFluidDefaults(), {});
            fluidSet = new imgix.FluidSet(options);
        }

        var fluidElements;
        if (elem && !imgix.helpers.isFluidSet(elem)) {
            fluidElements = Array.isArray(elem) ? elem : [elem];
        } else {
            var cls = options.fluidClass.toString();
            cls = cls.slice(0, 1) === '.' ? cls : ('.' + cls);
            fluidElements = (node || document).querySelectorAll(cls);
            if (node && imgix.helpers.matchesSelector(node, cls)) {
                fluidElements = Array.prototype.slice.call(fluidElements);
                fluidElements.unshift(node);
            }
        }

        for (var j = 0; j < fluidElements.length; j++) {
            if (fluidElements[j] === null) {
                continue;
            }

            if (options.updateOnPinchZoom) {
                fluidSet.attachGestureEvent(fluidElements[j]);
            }

            fluidSet.updateSrc(fluidElements[j]);
        }

        if (options.lazyLoad && !fluidSet.windowScrollEventBound) {
            fluidSet.attachScrollListener();
        }

        if (options.updateOnResize && !fluidSet.windowResizeEventBound) {
            fluidSet.attachWindowResizer();
        }

        return fluidSet;
    };

    'use strict';

    if (typeof window !== 'undefined') {
        /**
         * Cross-browser DOM ready helper
         * Dustin Diaz <dustindiaz.com> (MIT License)
         * https://github.com/ded/domready/tree/v0.3.0
         */

        /**
         * Runs a function when the DOM is ready (similar to jQuery.ready)
         * @memberof imgix
         * @static
         * @param {function} ready the function to run when the DOM is ready.
         */
        imgix.onready = (function () {
            var ready,
                listener,
                callbacks = [],
                ieHack = document.documentElement.doScroll,
                loadedRgx = ieHack ? /^loaded|^c/ : /^loaded|c/,
                loaded = loadedRgx.test(document.readyState);

            function flush() {
                var callback;

                loaded = true;
                while (callback = callbacks.shift()) {
                    callback();
                }
            }

            if (document.addEventListener) {
                listener = function () {
                    document.removeEventListener('DOMContentLoaded', listener, false);
                    flush();
                }

                document.addEventListener('DOMContentLoaded', listener, false);
            } else if (document.attachEvent) {
                listener = function () {
                    if (/^c/.test(document.readyState)) {
                        document.detachEvent('onreadystatechange', listener);
                        flush();
                    }
                }

                document.attachEvent('onreadystatechange', listener);
            }

            if (!!ieHack) {
                ready = function (callback) {
                    if (window.self != window.top) {
                        // We're in an iframe here
                        if (loaded) {
                            callback();
                        } else {
                            callbacks.push(callback);
                        }
                    } else {
                        // In a top-level window
                        (function () {
                            try {
                                document.documentElement.doScroll('left');
                            } catch (e) {
                                return setTimeout(function () {
                                    ready(callback);
                                }, 50);
                            }
                            callback();
                        })();
                    }
                };

            } else {
                ready = function (callback) {
                    if (loaded) {
                        callback();
                    } else {
                        callbacks.push(callback);
                    }
                };
            }

            return ready;
        })();
    }

    'use strict';

    /**
     * Helper method to turn rgb(255, 255, 255) style colors to hex (ffffff)
     * @memberof imgix
     * @static
     * @param {string} color in rgb(255, 255, 255) format
     * @returns {string} passed color converted to hex
     */
    imgix.rgbToHex = function (value) {
        var parts = value.split(',');

        parts = parts.map(function (a) {
            return imgix.componentToHex(parseInt(a.replace(/\D/g, '')));
        });

        return parts.join('');
    };

    imgix.componentToHex = function (c) {
        var hex = c.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    };

    /**
     * Gives a brightness score for a given color (higher is brighter)
     * @memberof imgix
     * @static
     * @param {string} color a color in rgb(r, g, b) format
     * @returns {Number} brightness score for the passed color
     */
    imgix.getColorBrightness = function (c) {
        if (c) {
            if (c.slice(0, 1) === '#') {
                c = imgix.hexToRGB(c);
            }
        } else {
            return 0;
        }

        var parts = c.replace(/[^0-9,]+/g, '').split(','),
            r = parseInt(parts[0], 10),
            g = parseInt(parts[1], 10),
            b = parseInt(parts[2], 10);

        return Math.sqrt((r * r * 0.241) + (g * g * 0.691) + (b * b * 0.068));
    };

    /**
     * Apply alpha to a RGB color string
     * @memberof imgix
     * @static
     * @param {string} color a color in rgb(r, g, b) format
     * @param {number} alpha aplpha amount 1=opaque 0=transparent
     * @returns {string} color in rgba format rgb(255, 0, 255, 0.5)
     */
    imgix.applyAlphaToRGB = function (rgb, alpha) {

        var pushAlpha = rgb.slice(0, 4) !== 'rgba',
            parts = rgb.split(',');

        parts = parts.map(function (a) {
            return parseInt(a.replace(/\D/g, ''), 10);
        });

        if (pushAlpha) {
            parts.push(alpha);
        } else if (parts.length === 4) {
            parts[3] = alpha;
        }

        return 'rgba(' + parts.join(', ') + ')';
    };

    /**
     * Converts a hex color to rgb (#ff00ff -> rgb(255, 0, 255)
     * @memberof imgix
     * @static
     * @param {string} color a color in hex format (#ff00ff)
     * @returns {string} color in rgb format rgb(255, 0, 255)
     */
    imgix.hexToRGB = function (hex) {

        if (hex) {
            if (hex.slice(0, 1) === '#') {
                hex = hex.slice(1, hex.length);
            } else if (hex.slice(0, 3) === 'rgb') {
                return hex;
            }
        }

        var r = 0,
            g = 0,
            b = 0;

        function dupe(x) {
            return (x + x).toString();
        }

        if (hex.length === 3) {
            r = parseInt(dupe(hex.slice(0, 1)), 16);
            g = parseInt(dupe(hex.slice(1, 2)), 16);
            b = parseInt(dupe(hex.slice(2, 3)), 16);
        } else if (hex.length === 6) {
            r = parseInt(hex.slice(0, 2), 16);
            g = parseInt(hex.slice(2, 4), 16);
            b = parseInt(hex.slice(4, 6), 16);
        } else {
            imgix.helpers.warn('invalid hex color:', hex);
        }

        return 'rgb(' + r + ', ' + g + ', ' + b + ')';
    };

    'use strict';

    /**
     * Get html element by auto-generated (via XPath) class name
     * @memberof imgix
     * @static
     * @private
     * @param {string} xpath the xpath of the element
     * @returns {Element} element with the xpath
     */
    imgix.getElementByXPathClassName = function (xpath) {
        return document.querySelector('.' + imgix.getXPathClass(xpath));
    };

    /**
     * Get image from an html element by auto-generated (via XPath) class name
     * @memberof imgix
     * @static
     * @private
     * @param {string} xpath the xpath of the element to get
     * @returns {string} url of image on the element
     */
    imgix.getElementImageByXPathClassName = function (xpath) {
        return imgix.getElementImage(imgix.getElementByXPathClassName(xpath));
    };

// Current: https://github.com/firebug/firebug/blob/5026362f2d1734adfcc4b44d5413065c50b27400/extension/content/firebug/lib/xpath.js
    imgix.getElementTreeXPath = function (element) {
        var paths = [];

        // Use nodeName (instead of localName) so namespace prefix is included (if any).
        for (; element && element.nodeType === Node.ELEMENT_NODE; element = element.parentNode) {
            var index = 0;
            for (var sibling = element.previousSibling; sibling; sibling = sibling.previousSibling) {
                // Ignore document type declaration.
                if (sibling.nodeType === Node.DOCUMENT_TYPE_NODE) {
                    continue;
                }

                if (sibling.nodeName === element.nodeName) {
                    ++index;
                }
            }

            var tagName = (element.prefix ? element.prefix + ':' : '') + element.localName,
                pathIndex = (index ? '[' + (index + 1) + ']' : '');

            paths.splice(0, 0, tagName + pathIndex);
        }

        return paths.length ? '/' + paths.join('/') : null;
    };

    if (typeof define === 'function' && define.amd) {
        define('imgix', [], function() {
            return imgix;
        });
    }

}).call(this);