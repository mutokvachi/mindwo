/*
  html2canvas 0.5.0-beta3 <http://html2canvas.hertzen.com>
  Copyright (c) 2016 Niklas von Hertzen

  Released under  License
*/

!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var f;"undefined"!=typeof window?f=window:"undefined"!=typeof global?f=global:"undefined"!=typeof self&&(f=self),f.html2canvas=e()}}(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
(function (global){
/*! http://mths.be/punycode v1.2.4 by @mathias */
;(function(root) {

	/** Detect free variables */
	var freeExports = typeof exports == 'object' && exports;
	var freeModule = typeof module == 'object' && module &&
		module.exports == freeExports && module;
	var freeGlobal = typeof global == 'object' && global;
	if (freeGlobal.global === freeGlobal || freeGlobal.window === freeGlobal) {
		root = freeGlobal;
	}

	/**
	 * The `punycode` object.
	 * @name punycode
	 * @type Object
	 */
	var punycode,

	/** Highest positive signed 32-bit float value */
	maxInt = 2147483647, // aka. 0x7FFFFFFF or 2^31-1

	/** Bootstring parameters */
	base = 36,
	tMin = 1,
	tMax = 26,
	skew = 38,
	damp = 700,
	initialBias = 72,
	initialN = 128, // 0x80
	delimiter = '-', // '\x2D'

	/** Regular expressions */
	regexPunycode = /^xn--/,
	regexNonASCII = /[^ -~]/, // unprintable ASCII chars + non-ASCII chars
	regexSeparators = /\x2E|\u3002|\uFF0E|\uFF61/g, // RFC 3490 separators

	/** Error messages */
	errors = {
		'overflow': 'Overflow: input needs wider integers to process',
		'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
		'invalid-input': 'Invalid input'
	},

	/** Convenience shortcuts */
	baseMinusTMin = base - tMin,
	floor = Math.floor,
	stringFromCharCode = String.fromCharCode,

	/** Temporary variable */
	key;

	/*--------------------------------------------------------------------------*/

	/**
	 * A generic error utility function.
	 * @private
	 * @param {String} type The error type.
	 * @returns {Error} Throws a `RangeError` with the applicable error message.
	 */
	function error(type) {
		throw RangeError(errors[type]);
	}

	/**
	 * A generic `Array#map` utility function.
	 * @private
	 * @param {Array} array The array to iterate over.
	 * @param {Function} callback The function that gets called for every array
	 * item.
	 * @returns {Array} A new array of values returned by the callback function.
	 */
	function map(array, fn) {
		var length = array.length;
		while (length--) {
			array[length] = fn(array[length]);
		}
		return array;
	}

	/**
	 * A simple `Array#map`-like wrapper to work with domain name strings.
	 * @private
	 * @param {String} domain The domain name.
	 * @param {Function} callback The function that gets called for every
	 * character.
	 * @returns {Array} A new string of characters returned by the callback
	 * function.
	 */
	function mapDomain(string, fn) {
		return map(string.split(regexSeparators), fn).join('.');
	}

	/**
	 * Creates an array containing the numeric code points of each Unicode
	 * character in the string. While JavaScript uses UCS-2 internally,
	 * this function will convert a pair of surrogate halves (each of which
	 * UCS-2 exposes as separate characters) into a single code point,
	 * matching UTF-16.
	 * @see `punycode.ucs2.encode`
	 * @see <http://mathiasbynens.be/notes/javascript-encoding>
	 * @memberOf punycode.ucs2
	 * @name decode
	 * @param {String} string The Unicode input string (UCS-2).
	 * @returns {Array} The new array of code points.
	 */
	function ucs2decode(string) {
		var output = [],
		    counter = 0,
		    length = string.length,
		    value,
		    extra;
		while (counter < length) {
			value = string.charCodeAt(counter++);
			if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
				// high surrogate, and there is a next character
				extra = string.charCodeAt(counter++);
				if ((extra & 0xFC00) == 0xDC00) { // low surrogate
					output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
				} else {
					// unmatched surrogate; only append this code unit, in case the next
					// code unit is the high surrogate of a surrogate pair
					output.push(value);
					counter--;
				}
			} else {
				output.push(value);
			}
		}
		return output;
	}

	/**
	 * Creates a string based on an array of numeric code points.
	 * @see `punycode.ucs2.decode`
	 * @memberOf punycode.ucs2
	 * @name encode
	 * @param {Array} codePoints The array of numeric code points.
	 * @returns {String} The new Unicode string (UCS-2).
	 */
	function ucs2encode(array) {
		return map(array, function(value) {
			var output = '';
			if (value > 0xFFFF) {
				value -= 0x10000;
				output += stringFromCharCode(value >>> 10 & 0x3FF | 0xD800);
				value = 0xDC00 | value & 0x3FF;
			}
			output += stringFromCharCode(value);
			return output;
		}).join('');
	}

	/**
	 * Converts a basic code point into a digit/integer.
	 * @see `digitToBasic()`
	 * @private
	 * @param {Number} codePoint The basic numeric code point value.
	 * @returns {Number} The numeric value of a basic code point (for use in
	 * representing integers) in the range `0` to `base - 1`, or `base` if
	 * the code point does not represent a value.
	 */
	function basicToDigit(codePoint) {
		if (codePoint - 48 < 10) {
			return codePoint - 22;
		}
		if (codePoint - 65 < 26) {
			return codePoint - 65;
		}
		if (codePoint - 97 < 26) {
			return codePoint - 97;
		}
		return base;
	}

	/**
	 * Converts a digit/integer into a basic code point.
	 * @see `basicToDigit()`
	 * @private
	 * @param {Number} digit The numeric value of a basic code point.
	 * @returns {Number} The basic code point whose value (when used for
	 * representing integers) is `digit`, which needs to be in the range
	 * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
	 * used; else, the lowercase form is used. The behavior is undefined
	 * if `flag` is non-zero and `digit` has no uppercase form.
	 */
	function digitToBasic(digit, flag) {
		//  0..25 map to ASCII a..z or A..Z
		// 26..35 map to ASCII 0..9
		return digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5);
	}

	/**
	 * Bias adaptation function as per section 3.4 of RFC 3492.
	 * http://tools.ietf.org/html/rfc3492#section-3.4
	 * @private
	 */
	function adapt(delta, numPoints, firstTime) {
		var k = 0;
		delta = firstTime ? floor(delta / damp) : delta >> 1;
		delta += floor(delta / numPoints);
		for (/* no initialization */; delta > baseMinusTMin * tMax >> 1; k += base) {
			delta = floor(delta / baseMinusTMin);
		}
		return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
	}

	/**
	 * Converts a Punycode string of ASCII-only symbols to a string of Unicode
	 * symbols.
	 * @memberOf punycode
	 * @param {String} input The Punycode string of ASCII-only symbols.
	 * @returns {String} The resulting string of Unicode symbols.
	 */
	function decode(input) {
		// Don't use UCS-2
		var output = [],
		    inputLength = input.length,
		    out,
		    i = 0,
		    n = initialN,
		    bias = initialBias,
		    basic,
		    j,
		    index,
		    oldi,
		    w,
		    k,
		    digit,
		    t,
		    /** Cached calculation results */
		    baseMinusT;

		// Handle the basic code points: let `basic` be the number of input code
		// points before the last delimiter, or `0` if there is none, then copy
		// the first basic code points to the output.

		basic = input.lastIndexOf(delimiter);
		if (basic < 0) {
			basic = 0;
		}

		for (j = 0; j < basic; ++j) {
			// if it's not a basic code point
			if (input.charCodeAt(j) >= 0x80) {
				error('not-basic');
			}
			output.push(input.charCodeAt(j));
		}

		// Main decoding loop: start just after the last delimiter if any basic code
		// points were copied; start at the beginning otherwise.

		for (index = basic > 0 ? basic + 1 : 0; index < inputLength; /* no final expression */) {

			// `index` is the index of the next character to be consumed.
			// Decode a generalized variable-length integer into `delta`,
			// which gets added to `i`. The overflow checking is easier
			// if we increase `i` as we go, then subtract off its starting
			// value at the end to obtain `delta`.
			for (oldi = i, w = 1, k = base; /* no condition */; k += base) {

				if (index >= inputLength) {
					error('invalid-input');
				}

				digit = basicToDigit(input.charCodeAt(index++));

				if (digit >= base || digit > floor((maxInt - i) / w)) {
					error('overflow');
				}

				i += digit * w;
				t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);

				if (digit < t) {
					break;
				}

				baseMinusT = base - t;
				if (w > floor(maxInt / baseMinusT)) {
					error('overflow');
				}

				w *= baseMinusT;

			}

			out = output.length + 1;
			bias = adapt(i - oldi, out, oldi == 0);

			// `i` was supposed to wrap around from `out` to `0`,
			// incrementing `n` each time, so we'll fix that now:
			if (floor(i / out) > maxInt - n) {
				error('overflow');
			}

			n += floor(i / out);
			i %= out;

			// Insert `n` at position `i` of the output
			output.splice(i++, 0, n);

		}

		return ucs2encode(output);
	}

	/**
	 * Converts a string of Unicode symbols to a Punycode string of ASCII-only
	 * symbols.
	 * @memberOf punycode
	 * @param {String} input The string of Unicode symbols.
	 * @returns {String} The resulting Punycode string of ASCII-only symbols.
	 */
	function encode(input) {
		var n,
		    delta,
		    handledCPCount,
		    basicLength,
		    bias,
		    j,
		    m,
		    q,
		    k,
		    t,
		    currentValue,
		    output = [],
		    /** `inputLength` will hold the number of code points in `input`. */
		    inputLength,
		    /** Cached calculation results */
		    handledCPCountPlusOne,
		    baseMinusT,
		    qMinusT;

		// Convert the input in UCS-2 to Unicode
		input = ucs2decode(input);

		// Cache the length
		inputLength = input.length;

		// Initialize the state
		n = initialN;
		delta = 0;
		bias = initialBias;

		// Handle the basic code points
		for (j = 0; j < inputLength; ++j) {
			currentValue = input[j];
			if (currentValue < 0x80) {
				output.push(stringFromCharCode(currentValue));
			}
		}

		handledCPCount = basicLength = output.length;

		// `handledCPCount` is the number of code points that have been handled;
		// `basicLength` is the number of basic code points.

		// Finish the basic string - if it is not empty - with a delimiter
		if (basicLength) {
			output.push(delimiter);
		}

		// Main encoding loop:
		while (handledCPCount < inputLength) {

			// All non-basic code points < n have been handled already. Find the next
			// larger one:
			for (m = maxInt, j = 0; j < inputLength; ++j) {
				currentValue = input[j];
				if (currentValue >= n && currentValue < m) {
					m = currentValue;
				}
			}

			// Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
			// but guard against overflow
			handledCPCountPlusOne = handledCPCount + 1;
			if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
				error('overflow');
			}

			delta += (m - n) * handledCPCountPlusOne;
			n = m;

			for (j = 0; j < inputLength; ++j) {
				currentValue = input[j];

				if (currentValue < n && ++delta > maxInt) {
					error('overflow');
				}

				if (currentValue == n) {
					// Represent delta as a generalized variable-length integer
					for (q = delta, k = base; /* no condition */; k += base) {
						t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
						if (q < t) {
							break;
						}
						qMinusT = q - t;
						baseMinusT = base - t;
						output.push(
							stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0))
						);
						q = floor(qMinusT / baseMinusT);
					}

					output.push(stringFromCharCode(digitToBasic(q, 0)));
					bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
					delta = 0;
					++handledCPCount;
				}
			}

			++delta;
			++n;

		}
		return output.join('');
	}

	/**
	 * Converts a Punycode string representing a domain name to Unicode. Only the
	 * Punycoded parts of the domain name will be converted, i.e. it doesn't
	 * matter if you call it on a string that has already been converted to
	 * Unicode.
	 * @memberOf punycode
	 * @param {String} domain The Punycode domain name to convert to Unicode.
	 * @returns {String} The Unicode representation of the given Punycode
	 * string.
	 */
	function toUnicode(domain) {
		return mapDomain(domain, function(string) {
			return regexPunycode.test(string)
				? decode(string.slice(4).toLowerCase())
				: string;
		});
	}

	/**
	 * Converts a Unicode string representing a domain name to Punycode. Only the
	 * non-ASCII parts of the domain name will be converted, i.e. it doesn't
	 * matter if you call it with a domain that's already in ASCII.
	 * @memberOf punycode
	 * @param {String} domain The domain name to convert, as a Unicode string.
	 * @returns {String} The Punycode representation of the given domain name.
	 */
	function toASCII(domain) {
		return mapDomain(domain, function(string) {
			return regexNonASCII.test(string)
				? 'xn--' + encode(string)
				: string;
		});
	}

	/*--------------------------------------------------------------------------*/

	/** Define the public API */
	punycode = {
		/**
		 * A string representing the current Punycode.js version number.
		 * @memberOf punycode
		 * @type String
		 */
		'version': '1.2.4',
		/**
		 * An object of methods to convert from JavaScript's internal character
		 * representation (UCS-2) to Unicode code points, and back.
		 * @see <http://mathiasbynens.be/notes/javascript-encoding>
		 * @memberOf punycode
		 * @type Object
		 */
		'ucs2': {
			'decode': ucs2decode,
			'encode': ucs2encode
		},
		'decode': decode,
		'encode': encode,
		'toASCII': toASCII,
		'toUnicode': toUnicode
	};

	/** Expose `punycode` */
	// Some AMD build optimizers, like r.js, check for specific condition patterns
	// like the following:
	if (
		typeof define == 'function' &&
		typeof define.amd == 'object' &&
		define.amd
	) {
		define('punycode', function() {
			return punycode;
		});
	} else if (freeExports && !freeExports.nodeType) {
		if (freeModule) { // in Node.js or RingoJS v0.8.0+
			freeModule.exports = punycode;
		} else { // in Narwhal or RingoJS v0.7.0-
			for (key in punycode) {
				punycode.hasOwnProperty(key) && (freeExports[key] = punycode[key]);
			}
		}
	} else { // in Rhino or a web browser
		root.punycode = punycode;
	}

}(this));

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}],2:[function(_dereq_,module,exports){
var log = _dereq_('./log');

function restoreOwnerScroll(ownerDocument, x, y) {
    if (ownerDocument.defaultView && (x !== ownerDocument.defaultView.pageXOffset || y !== ownerDocument.defaultView.pageYOffset)) {
        ownerDocument.defaultView.scrollTo(x, y);
    }
}

function cloneCanvasContents(canvas, clonedCanvas) {
    try {
        if (clonedCanvas) {
            clonedCanvas.width = canvas.width;
            clonedCanvas.height = canvas.height;
            clonedCanvas.getContext("2d").putImageData(canvas.getContext("2d").getImageData(0, 0, canvas.width, canvas.height), 0, 0);
        }
    } catch(e) {
        log("Unable to copy canvas content from", canvas, e);
    }
}

function cloneNode(node, javascriptEnabled) {
    var clone = node.nodeType === 3 ? document.createTextNode(node.nodeValue) : node.cloneNode(false);

    var child = node.firstChild;
    while(child) {
        if (javascriptEnabled === true || child.nodeType !== 1 || child.nodeName !== 'SCRIPT') {
            clone.appendChild(cloneNode(child, javascriptEnabled));
        }
        child = child.nextSibling;
    }

    if (node.nodeType === 1) {
        clone._scrollTop = node.scrollTop;
        clone._scrollLeft = node.scrollLeft;
        if (node.nodeName === "CANVAS") {
            cloneCanvasContents(node, clone);
        } else if (node.nodeName === "TEXTAREA" || node.nodeName === "SELECT") {
            clone.value = node.value;
        }
    }

    return clone;
}

function initNode(node) {
    if (node.nodeType === 1) {
        node.scrollTop = node._scrollTop;
        node.scrollLeft = node._scrollLeft;

        var child = node.firstChild;
        while(child) {
            initNode(child);
            child = child.nextSibling;
        }
    }
}

module.exports = function(ownerDocument, containerDocument, width, height, options, x ,y) {
    var documentElement = cloneNode(ownerDocument.documentElement, options.javascriptEnabled);
    var container = containerDocument.createElement("iframe");

    container.className = "html2canvas-container";
    container.style.visibility = "hidden";
    container.style.position = "fixed";
    container.style.left = "-10000px";
    container.style.top = "0px";
    container.style.border = "0";
    container.width = width;
    container.height = height;
    container.scrolling = "no"; // ios won't scroll without it
    containerDocument.body.appendChild(container);

    return new Promise(function(resolve) {
        var documentClone = container.contentWindow.document;

        /* Chrome doesn't detect relative background-images assigned in inline <style> sheets when fetched through getComputedStyle
         if window url is about:blank, we can assign the url to current by writing onto the document
         */
        container.contentWindow.onload = container.onload = function() {
            var interval = setInterval(function() {
                if (documentClone.body.childNodes.length > 0) {
                    initNode(documentClone.documentElement);
                    clearInterval(interval);
                    if (options.type === "view") {
                        container.contentWindow.scrollTo(x, y);
                        if ((/(iPad|iPhone|iPod)/g).test(navigator.userAgent) && (container.contentWindow.scrollY !== y || container.contentWindow.scrollX !== x)) {
                            documentClone.documentElement.style.top = (-y) + "px";
                            documentClone.documentElement.style.left = (-x) + "px";
                            documentClone.documentElement.style.position = 'absolute';
                        }
                    }
                    resolve(container);
                }
            }, 50);
        };

        documentClone.open();
        documentClone.write("<!DOCTYPE html><html></html>");
        // Chrome scrolls the parent document for some reason after the write to the cloned window???
        restoreOwnerScroll(ownerDocument, x, y);
        documentClone.replaceChild(documentClone.adoptNode(documentElement), documentClone.documentElement);
        documentClone.close();
    });
};

},{"./log":13}],3:[function(_dereq_,module,exports){
// http://dev.w3.org/csswg/css-color/

function Color(value) {
    this.r = 0;
    this.g = 0;
    this.b = 0;
    this.a = null;
    var result = this.fromArray(value) ||
        this.namedColor(value) ||
        this.rgb(value) ||
        this.rgba(value) ||
        this.hex6(value) ||
        this.hex3(value);
}

Color.prototype.darken = function(amount) {
    var a = 1 - amount;
    return  new Color([
        Math.round(this.r * a),
        Math.round(this.g * a),
        Math.round(this.b * a),
        this.a
    ]);
};

Color.prototype.isTransparent = function() {
    return this.a === 0;
};

Color.prototype.isBlack = function() {
    return this.r === 0 && this.g === 0 && this.b === 0;
};

Color.prototype.fromArray = function(array) {
    if (Array.isArray(array)) {
        this.r = Math.min(array[0], 255);
        this.g = Math.min(array[1], 255);
        this.b = Math.min(array[2], 255);
        if (array.length > 3) {
            this.a = array[3];
        }
    }

    return (Array.isArray(array));
};

var _hex3 = /^#([a-f0-9]{3})$/i;

Color.prototype.hex3 = function(value) {
    var match = null;
    if ((match = value.match(_hex3)) !== null) {
        this.r = parseInt(match[1][0] + match[1][0], 16);
        this.g = parseInt(match[1][1] + match[1][1], 16);
        this.b = parseInt(match[1][2] + match[1][2], 16);
    }
    return match !== null;
};

var _hex6 = /^#([a-f0-9]{6})$/i;

Color.prototype.hex6 = function(value) {
    var match = null;
    if ((match = value.match(_hex6)) !== null) {
        this.r = parseInt(match[1].substring(0, 2), 16);
        this.g = parseInt(match[1].substring(2, 4), 16);
        this.b = parseInt(match[1].substring(4, 6), 16);
    }
    return match !== null;
};


var _rgb = /^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/;

Color.prototype.rgb = function(value) {
    var match = null;
    if ((match = value.match(_rgb)) !== null) {
        this.r = Number(match[1]);
        this.g = Number(match[2]);
        this.b = Number(match[3]);
    }
    return match !== null;
};

var _rgba = /^rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d?\.?\d+)\s*\)$/;

Color.prototype.rgba = function(value) {
    var match = null;
    if ((match = value.match(_rgba)) !== null) {
        this.r = Number(match[1]);
        this.g = Number(match[2]);
        this.b = Number(match[3]);
        this.a = Number(match[4]);
    }
    return match !== null;
};

Color.prototype.toString = function() {
    return this.a !== null && this.a !== 1 ?
    "rgba(" + [this.r, this.g, this.b, this.a].join(",") + ")" :
    "rgb(" + [this.r, this.g, this.b].join(",") + ")";
};

Color.prototype.namedColor = function(value) {
    value = value.toLowerCase();
    var color = colors[value];
    if (color) {
        this.r = color[0];
        this.g = color[1];
        this.b = color[2];
    } else if (value === "transparent") {
        this.r = this.g = this.b = this.a = 0;
        return true;
    }

    return !!color;
};

Color.prototype.isColor = true;

// JSON.stringify([].slice.call($$('.named-color-table tr'), 1).map(function(row) { return [row.childNodes[3].textContent, row.childNodes[5].textContent.trim().split(",").map(Number)] }).reduce(function(data, row) {data[row[0]] = row[1]; return data}, {}))
var colors = {
    "aliceblue": [240, 248, 255],
    "antiquewhite": [250, 235, 215],
    "aqua": [0, 255, 255],
    "aquamarine": [127, 255, 212],
    "azure": [240, 255, 255],
    "beige": [245, 245, 220],
    "bisque": [255, 228, 196],
    "black": [0, 0, 0],
    "blanchedalmond": [255, 235, 205],
    "blue": [0, 0, 255],
    "blueviolet": [138, 43, 226],
    "brown": [165, 42, 42],
    "burlywood": [222, 184, 135],
    "cadetblue": [95, 158, 160],
    "chartreuse": [127, 255, 0],
    "chocolate": [210, 105, 30],
    "coral": [255, 127, 80],
    "cornflowerblue": [100, 149, 237],
    "cornsilk": [255, 248, 220],
    "crimson": [220, 20, 60],
    "cyan": [0, 255, 255],
    "darkblue": [0, 0, 139],
    "darkcyan": [0, 139, 139],
    "darkgoldenrod": [184, 134, 11],
    "darkgray": [169, 169, 169],
    "darkgreen": [0, 100, 0],
    "darkgrey": [169, 169, 169],
    "darkkhaki": [189, 183, 107],
    "darkmagenta": [139, 0, 139],
    "darkolivegreen": [85, 107, 47],
    "darkorange": [255, 140, 0],
    "darkorchid": [153, 50, 204],
    "darkred": [139, 0, 0],
    "darksalmon": [233, 150, 122],
    "darkseagreen": [143, 188, 143],
    "darkslateblue": [72, 61, 139],
    "darkslategray": [47, 79, 79],
    "darkslategrey": [47, 79, 79],
    "darkturquoise": [0, 206, 209],
    "darkviolet": [148, 0, 211],
    "deeppink": [255, 20, 147],
    "deepskyblue": [0, 191, 255],
    "dimgray": [105, 105, 105],
    "dimgrey": [105, 105, 105],
    "dodgerblue": [30, 144, 255],
    "firebrick": [178, 34, 34],
    "floralwhite": [255, 250, 240],
    "forestgreen": [34, 139, 34],
    "fuchsia": [255, 0, 255],
    "gainsboro": [220, 220, 220],
    "ghostwhite": [248, 248, 255],
    "gold": [255, 215, 0],
    "goldenrod": [218, 165, 32],
    "gray": [128, 128, 128],
    "green": [0, 128, 0],
    "greenyellow": [173, 255, 47],
    "grey": [128, 128, 128],
    "honeydew": [240, 255, 240],
    "hotpink": [255, 105, 180],
    "indianred": [205, 92, 92],
    "indigo": [75, 0, 130],
    "ivory": [255, 255, 240],
    "khaki": [240, 230, 140],
    "lavender": [230, 230, 250],
    "lavenderblush": [255, 240, 245],
    "lawngreen": [124, 252, 0],
    "lemonchiffon": [255, 250, 205],
    "lightblue": [173, 216, 230],
    "lightcoral": [240, 128, 128],
    "lightcyan": [224, 255, 255],
    "lightgoldenrodyellow": [250, 250, 210],
    "lightgray": [211, 211, 211],
    "lightgreen": [144, 238, 144],
    "lightgrey": [211, 211, 211],
    "lightpink": [255, 182, 193],
    "lightsalmon": [255, 160, 122],
    "lightseagreen": [32, 178, 170],
    "lightskyblue": [135, 206, 250],
    "lightslategray": [119, 136, 153],
    "lightslategrey": [119, 136, 153],
    "lightsteelblue": [176, 196, 222],
    "lightyellow": [255, 255, 224],
    "lime": [0, 255, 0],
    "limegreen": [50, 205, 50],
    "linen": [250, 240, 230],
    "magenta": [255, 0, 255],
    "maroon": [128, 0, 0],
    "mediumaquamarine": [102, 205, 170],
    "mediumblue": [0, 0, 205],
    "mediumorchid": [186, 85, 211],
    "mediumpurple": [147, 112, 219],
    "mediumseagreen": [60, 179, 113],
    "mediumslateblue": [123, 104, 238],
    "mediumspringgreen": [0, 250, 154],
    "mediumturquoise": [72, 209, 204],
    "mediumvioletred": [199, 21, 133],
    "midnightblue": [25, 25, 112],
    "mintcream": [245, 255, 250],
    "mistyrose": [255, 228, 225],
    "moccasin": [255, 228, 181],
    "navajowhite": [255, 222, 173],
    "navy": [0, 0, 128],
    "oldlace": [253, 245, 230],
    "olive": [128, 128, 0],
    "olivedrab": [107, 142, 35],
    "orange": [255, 165, 0],
    "orangered": [255, 69, 0],
    "orchid": [218, 112, 214],
    "palegoldenrod": [238, 232, 170],
    "palegreen": [152, 251, 152],
    "paleturquoise": [175, 238, 238],
    "palevioletred": [219, 112, 147],
    "papayawhip": [255, 239, 213],
    "peachpuff": [255, 218, 185],
    "peru": [205, 133, 63],
    "pink": [255, 192, 203],
    "plum": [221, 160, 221],
    "powderblue": [176, 224, 230],
    "purple": [128, 0, 128],
    "rebeccapurple": [102, 51, 153],
    "red": [255, 0, 0],
    "rosybrown": [188, 143, 143],
    "royalblue": [65, 105, 225],
    "saddlebrown": [139, 69, 19],
    "salmon": [250, 128, 114],
    "sandybrown": [244, 164, 96],
    "seagreen": [46, 139, 87],
    "seashell": [255, 245, 238],
    "sienna": [160, 82, 45],
    "silver": [192, 192, 192],
    "skyblue": [135, 206, 235],
    "slateblue": [106, 90, 205],
    "slategray": [112, 128, 144],
    "slategrey": [112, 128, 144],
    "snow": [255, 250, 250],
    "springgreen": [0, 255, 127],
    "steelblue": [70, 130, 180],
    "tan": [210, 180, 140],
    "teal": [0, 128, 128],
    "thistle": [216, 191, 216],
    "tomato": [255, 99, 71],
    "turquoise": [64, 224, 208],
    "violet": [238, 130, 238],
    "wheat": [245, 222, 179],
    "white": [255, 255, 255],
    "whitesmoke": [245, 245, 245],
    "yellow": [255, 255, 0],
    "yellowgreen": [154, 205, 50]
};

module.exports = Color;

},{}],4:[function(_dereq_,module,exports){
var Support = _dereq_('./support');
var CanvasRenderer = _dereq_('./renderers/canvas');
var ImageLoader = _dereq_('./imageloader');
var NodeParser = _dereq_('./nodeparser');
var NodeContainer = _dereq_('./nodecontainer');
var log = _dereq_('./log');
var utils = _dereq_('./utils');
var createWindowClone = _dereq_('./clone');
var loadUrlDocument = _dereq_('./proxy').loadUrlDocument;
var getBounds = utils.getBounds;

var html2canvasNodeAttribute = "data-html2canvas-node";
var html2canvasCloneIndex = 0;

function html2canvas(nodeList, options) {
    var index = html2canvasCloneIndex++;
    options = options || {};
    if (options.logging) {
        log.options.logging = true;
        log.options.start = Date.now();
    }

    options.async = typeof(options.async) === "undefined" ? true : options.async;
    options.allowTaint = typeof(options.allowTaint) === "undefined" ? false : options.allowTaint;
    options.removeContainer = typeof(options.removeContainer) === "undefined" ? true : options.removeContainer;
    options.javascriptEnabled = typeof(options.javascriptEnabled) === "undefined" ? false : options.javascriptEnabled;
    options.imageTimeout = typeof(options.imageTimeout) === "undefined" ? 10000 : options.imageTimeout;
    options.renderer = typeof(options.renderer) === "function" ? options.renderer : CanvasRenderer;
    options.strict = !!options.strict;

    if (typeof(nodeList) === "string") {
        if (typeof(options.proxy) !== "string") {
            return Promise.reject("Proxy must be used when rendering url");
        }
        var width = options.width != null ? options.width : window.innerWidth;
        var height = options.height != null ? options.height : window.innerHeight;
        return loadUrlDocument(absoluteUrl(nodeList), options.proxy, document, width, height, options).then(function(container) {
            return renderWindow(container.contentWindow.document.documentElement, container, options, width, height);
        });
    }

    var node = ((nodeList === undefined) ? [document.documentElement] : ((nodeList.length) ? nodeList : [nodeList]))[0];
    node.setAttribute(html2canvasNodeAttribute + index, index);
    return renderDocument(node.ownerDocument, options, node.ownerDocument.defaultView.innerWidth, node.ownerDocument.defaultView.innerHeight, index).then(function(canvas) {
        if (typeof(options.onrendered) === "function") {
            log("options.onrendered is deprecated, html2canvas returns a Promise containing the canvas");
            options.onrendered(canvas);
        }
        return canvas;
    });
}

html2canvas.CanvasRenderer = CanvasRenderer;
html2canvas.NodeContainer = NodeContainer;
html2canvas.log = log;
html2canvas.utils = utils;

var html2canvasExport = (typeof(document) === "undefined" || typeof(Object.create) !== "function" || typeof(document.createElement("canvas").getContext) !== "function") ? function() {
    return Promise.reject("No canvas support");
} : html2canvas;

module.exports = html2canvasExport;

if (typeof(define) === 'function' && define.amd) {
    define('html2canvas', [], function() {
        return html2canvasExport;
    });
}

function renderDocument(document, options, windowWidth, windowHeight, html2canvasIndex) {
    return createWindowClone(document, document, windowWidth, windowHeight, options, document.defaultView.pageXOffset, document.defaultView.pageYOffset).then(function(container) {
        log("Document cloned");
        var attributeName = html2canvasNodeAttribute + html2canvasIndex;
        var selector = "[" + attributeName + "='" + html2canvasIndex + "']";
        document.querySelector(selector).removeAttribute(attributeName);
        var clonedWindow = container.contentWindow;
        var node = clonedWindow.document.querySelector(selector);
        var oncloneHandler = (typeof(options.onclone) === "function") ? Promise.resolve(options.onclone(clonedWindow.document)) : Promise.resolve(true);
        return oncloneHandler.then(function() {
            return renderWindow(node, container, options, windowWidth, windowHeight);
        });
    });
}

function renderWindow(node, container, options, windowWidth, windowHeight) {
    var clonedWindow = container.contentWindow;
    var support = new Support(clonedWindow.document);
    var imageLoader = new ImageLoader(options, support);
    var bounds = getBounds(node);
    var width = options.type === "view" ? windowWidth : documentWidth(clonedWindow.document);
    var height = options.type === "view" ? windowHeight : documentHeight(clonedWindow.document);
    var renderer = new options.renderer(width, height, imageLoader, options, document);
    var parser = new NodeParser(node, renderer, support, imageLoader, options);
    return parser.ready.then(function() {
        log("Finished rendering");
        var canvas;

        if (options.type === "view") {
            canvas = crop(renderer.canvas, {width: renderer.canvas.width, height: renderer.canvas.height, top: 0, left: 0, x: 0, y: 0});
        } else if (node === clonedWindow.document.body || node === clonedWindow.document.documentElement || options.canvas != null) {
            canvas = renderer.canvas;
        } else {
            canvas = crop(renderer.canvas, {width:  options.width != null ? options.width : bounds.width, height: options.height != null ? options.height : bounds.height, top: bounds.top, left: bounds.left, x: 0, y: 0});
        }

        cleanupContainer(container, options);
        return canvas;
    });
}

function cleanupContainer(container, options) {
    if (options.removeContainer) {
        container.parentNode.removeChild(container);
        log("Cleaned up container");
    }
}

function crop(canvas, bounds) {
    var croppedCanvas = document.createElement("canvas");
    var x1 = Math.min(canvas.width - 1, Math.max(0, bounds.left));
    var x2 = Math.min(canvas.width, Math.max(1, bounds.left + bounds.width));
    var y1 = Math.min(canvas.height - 1, Math.max(0, bounds.top));
    var y2 = Math.min(canvas.height, Math.max(1, bounds.top + bounds.height));
    croppedCanvas.width = bounds.width;
    croppedCanvas.height =  bounds.height;
    var width = x2-x1;
    var height = y2-y1;
    log("Cropping canvas at:", "left:", bounds.left, "top:", bounds.top, "width:", width, "height:", height);
    log("Resulting crop with width", bounds.width, "and height", bounds.height, "with x", x1, "and y", y1);
    croppedCanvas.getContext("2d").drawImage(canvas, x1, y1, width, height, bounds.x, bounds.y, width, height);
    return croppedCanvas;
}

function documentWidth (doc) {
    return Math.max(
        Math.max(doc.body.scrollWidth, doc.documentElement.scrollWidth),
        Math.max(doc.body.offsetWidth, doc.documentElement.offsetWidth),
        Math.max(doc.body.clientWidth, doc.documentElement.clientWidth)
    );
}

function documentHeight (doc) {
    return Math.max(
        Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight),
        Math.max(doc.body.offsetHeight, doc.documentElement.offsetHeight),
        Math.max(doc.body.clientHeight, doc.documentElement.clientHeight)
    );
}

function absoluteUrl(url) {
    var link = document.createElement("a");
    link.href = url;
    link.href = link.href;
    return link;
}

},{"./clone":2,"./imageloader":11,"./log":13,"./nodecontainer":14,"./nodeparser":15,"./proxy":16,"./renderers/canvas":20,"./support":22,"./utils":26}],5:[function(_dereq_,module,exports){
var log = _dereq_('./log');
var smallImage = _dereq_('./utils').smallImage;

function DummyImageContainer(src) {
    this.src = src;
    log("DummyImageContainer for", src);
    if (!this.promise || !this.image) {
        log("Initiating DummyImageContainer");
        DummyImageContainer.prototype.image = new Image();
        var image = this.image;
        DummyImageContainer.prototype.promise = new Promise(function(resolve, reject) {
            image.onload = resolve;
            image.onerror = reject;
            image.src = smallImage();
            if (image.complete === true) {
                resolve(image);
            }
        });
    }
}

module.exports = DummyImageContainer;

},{"./log":13,"./utils":26}],6:[function(_dereq_,module,exports){
var smallImage = _dereq_('./utils').smallImage;

function Font(family, size) {
    var container = document.createElement('div'),
        img = document.createElement('img'),
        span = document.createElement('span'),
        sampleText = 'Hidden Text',
        baseline,
        middle;

    container.style.visibility = "hidden";
    container.style.fontFamily = family;
    container.style.fontSize = size;
    container.style.margin = 0;
    container.style.padding = 0;

    document.body.appendChild(container);

    img.src = smallImage();
    img.width = 1;
    img.height = 1;

    img.style.margin = 0;
    img.style.padding = 0;
    img.style.verticalAlign = "baseline";

    span.style.fontFamily = family;
    span.style.fontSize = size;
    span.style.margin = 0;
    span.style.padding = 0;

    span.appendChild(document.createTextNode(sampleText));
    container.appendChild(span);
    container.appendChild(img);
    baseline = (img.offsetTop - span.offsetTop) + 1;

    container.removeChild(span);
    container.appendChild(document.createTextNode(sampleText));

    container.style.lineHeight = "normal";
    img.style.verticalAlign = "super";

    middle = (img.offsetTop-container.offsetTop) + 1;

    document.body.removeChild(container);

    this.baseline = baseline;
    this.lineWidth = 1;
    this.middle = middle;
}

module.exports = Font;

},{"./utils":26}],7:[function(_dereq_,module,exports){
var Font = _dereq_('./font');

function FontMetrics() {
    this.data = {};
}

FontMetrics.prototype.getMetrics = function(family, size) {
    if (this.data[family + "-" + size] === undefined) {
        this.data[family + "-" + size] = new Font(family, size);
    }
    return this.data[family + "-" + size];
};

module.exports = FontMetrics;

},{"./font":6}],8:[function(_dereq_,module,exports){
var utils = _dereq_('./utils');
var getBounds = utils.getBounds;
var loadUrlDocument = _dereq_('./proxy').loadUrlDocument;

function FrameContainer(container, sameOrigin, options) {
    this.image = null;
    this.src = container;
    var self = this;
    var bounds = getBounds(container);
    this.promise = (!sameOrigin ? this.proxyLoad(options.proxy, bounds, options) : new Promise(function(resolve) {
        if (container.contentWindow.document.URL === "about:blank" || container.contentWindow.document.documentElement == null) {
            container.contentWindow.onload = container.onload = function() {
                resolve(container);
            };
        } else {
            resolve(container);
        }
    })).then(function(container) {
        var html2canvas = _dereq_('./core');
        return html2canvas(container.contentWindow.document.documentElement, {type: 'view', width: container.width, height: container.height, proxy: options.proxy, javascriptEnabled: options.javascriptEnabled, removeContainer: options.removeContainer, allowTaint: options.allowTaint, imageTimeout: options.imageTimeout / 2});
    }).then(function(canvas) {
        return self.image = canvas;
    });
}

FrameContainer.prototype.proxyLoad = function(proxy, bounds, options) {
    var container = this.src;
    return loadUrlDocument(container.src, proxy, container.ownerDocument, bounds.width, bounds.height, options);
};

module.exports = FrameContainer;

},{"./core":4,"./proxy":16,"./utils":26}],9:[function(_dereq_,module,exports){
function GradientContainer(imageData) {
    this.src = imageData.value;
    this.colorStops = [];
    this.type = null;
    this.x0 = 0.5;
    this.y0 = 0.5;
    this.x1 = 0.5;
    this.y1 = 0.5;
    this.promise = Promise.resolve(true);
}

GradientContainer.TYPES = {
    LINEAR: 1,
    RADIAL: 2
};

// TODO: support hsl[a], negative %/length values
// TODO: support <angle> (e.g. -?\d{1,3}(?:\.\d+)deg, etc. : https://developer.mozilla.org/docs/Web/CSS/angle )
GradientContainer.REGEXP_COLORSTOP = /^\s*(rgba?\(\s*\d{1,3},\s*\d{1,3},\s*\d{1,3}(?:,\s*[0-9\.]+)?\s*\)|[a-z]{3,20}|#[a-f0-9]{3,6})(?:\s+(\d{1,3}(?:\.\d+)?)(%|px)?)?(?:\s|$)/i;

module.exports = GradientContainer;

},{}],10:[function(_dereq_,module,exports){
function ImageContainer(src, cors) {
    this.src = src;
    this.image = new Image();
    var self = this;
    this.tainted = null;
    this.promise = new Promise(function(resolve, reject) {
        self.image.onload = resolve;
        self.image.onerror = reject;
        if (cors) {
            self.image.crossOrigin = "anonymous";
        }
        self.image.src = src;
        if (self.image.complete === true) {
            resolve(self.image);
        }
    });
}

module.exports = ImageContainer;

},{}],11:[function(_dereq_,module,exports){
var log = _dereq_('./log');
var ImageContainer = _dereq_('./imagecontainer');
var DummyImageContainer = _dereq_('./dummyimagecontainer');
var ProxyImageContainer = _dereq_('./proxyimagecontainer');
var FrameContainer = _dereq_('./framecontainer');
var SVGContainer = _dereq_('./svgcontainer');
var SVGNodeContainer = _dereq_('./svgnodecontainer');
var LinearGradientContainer = _dereq_('./lineargradientcontainer');
var WebkitGradientContainer = _dereq_('./webkitgradientcontainer');
var bind = _dereq_('./utils').bind;

function ImageLoader(options, support) {
    this.link = null;
    this.options = options;
    this.support = support;
    this.origin = this.getOrigin(window.location.href);
}

ImageLoader.prototype.findImages = function(nodes) {
    var images = [];
    nodes.reduce(function(imageNodes, container) {
        switch(container.node.nodeName) {
        case "IMG":
            return imageNodes.concat([{
                args: [container.node.src],
                method: "url"
            }]);
        case "svg":
        case "IFRAME":
            return imageNodes.concat([{
                args: [container.node],
                method: container.node.nodeName
            }]);
        }
        return imageNodes;
    }, []).forEach(this.addImage(images, this.loadImage), this);
    return images;
};

ImageLoader.prototype.findBackgroundImage = function(images, container) {
    container.parseBackgroundImages().filter(this.hasImageBackground).forEach(this.addImage(images, this.loadImage), this);
    return images;
};

ImageLoader.prototype.addImage = function(images, callback) {
    return function(newImage) {
        newImage.args.forEach(function(image) {
            if (!this.imageExists(images, image)) {
                images.splice(0, 0, callback.call(this, newImage));
                log('Added image #' + (images.length), typeof(image) === "string" ? image.substring(0, 100) : image);
            }
        }, this);
    };
};

ImageLoader.prototype.hasImageBackground = function(imageData) {
    return imageData.method !== "none";
};

ImageLoader.prototype.loadImage = function(imageData) {
    if (imageData.method === "url") {
        var src = imageData.args[0];
        if (this.isSVG(src) && !this.support.svg && !this.options.allowTaint) {
            return new SVGContainer(src);
        } else if (src.match(/data:image\/.*;base64,/i)) {
            return new ImageContainer(src.replace(/url\(['"]{0,}|['"]{0,}\)$/ig, ''), false);
        } else if (this.isSameOrigin(src) || this.options.allowTaint === true || this.isSVG(src)) {
            return new ImageContainer(src, false);
        } else if (this.support.cors && !this.options.allowTaint && this.options.useCORS) {
            return new ImageContainer(src, true);
        } else if (this.options.proxy) {
            return new ProxyImageContainer(src, this.options.proxy);
        } else {
            return new DummyImageContainer(src);
        }
    } else if (imageData.method === "linear-gradient") {
        return new LinearGradientContainer(imageData);
    } else if (imageData.method === "gradient") {
        return new WebkitGradientContainer(imageData);
    } else if (imageData.method === "svg") {
        return new SVGNodeContainer(imageData.args[0], this.support.svg);
    } else if (imageData.method === "IFRAME") {
        return new FrameContainer(imageData.args[0], this.isSameOrigin(imageData.args[0].src), this.options);
    } else {
        return new DummyImageContainer(imageData);
    }
};

ImageLoader.prototype.isSVG = function(src) {
    return src.substring(src.length - 3).toLowerCase() === "svg" || SVGContainer.prototype.isInline(src);
};

ImageLoader.prototype.imageExists = function(images, src) {
    return images.some(function(image) {
        return image.src === src;
    });
};

ImageLoader.prototype.isSameOrigin = function(url) {
    return (this.getOrigin(url) === this.origin);
};

ImageLoader.prototype.getOrigin = function(url) {
    var link = this.link || (this.link = document.createElement("a"));
    link.href = url;
    link.href = link.href; // IE9, LOL! - http://jsfiddle.net/niklasvh/2e48b/
    return link.protocol + link.hostname + link.port;
};

ImageLoader.prototype.getPromise = function(container) {
    return this.timeout(container, this.options.imageTimeout)['catch'](function() {
        var dummy = new DummyImageContainer(container.src);
        return dummy.promise.then(function(image) {
            container.image = image;
        });
    });
};

ImageLoader.prototype.get = function(src) {
    var found = null;
    return this.images.some(function(img) {
        return (found = img).src === src;
    }) ? found : null;
};

ImageLoader.prototype.fetch = function(nodes) {
    this.images = nodes.reduce(bind(this.findBackgroundImage, this), this.findImages(nodes));
    this.images.forEach(function(image, index) {
        image.promise.then(function() {
            log("Succesfully loaded image #"+ (index+1), image);
        }, function(e) {
            log("Failed loading image #"+ (index+1), image, e);
        });
    });
    this.ready = Promise.all(this.images.map(this.getPromise, this));
    log("Finished searching images");
    return this;
};

ImageLoader.prototype.timeout = function(container, timeout) {
    var timer;
    var promise = Promise.race([container.promise, new Promise(function(res, reject) {
        timer = setTimeout(function() {
            log("Timed out loading image", container);
            reject(container);
        }, timeout);
    })]).then(function(container) {
        clearTimeout(timer);
        return container;
    });
    promise['catch'](function() {
        clearTimeout(timer);
    });
    return promise;
};

module.exports = ImageLoader;

},{"./dummyimagecontainer":5,"./framecontainer":8,"./imagecontainer":10,"./lineargradientcontainer":12,"./log":13,"./proxyimagecontainer":17,"./svgcontainer":23,"./svgnodecontainer":24,"./utils":26,"./webkitgradientcontainer":27}],12:[function(_dereq_,module,exports){
var GradientContainer = _dereq_('./gradientcontainer');
var Color = _dereq_('./color');

function LinearGradientContainer(imageData) {
    GradientContainer.apply(this, arguments);
    this.type = GradientContainer.TYPES.LINEAR;

    var hasDirection = LinearGradientContainer.REGEXP_DIRECTION.test( imageData.args[0] ) ||
        !GradientContainer.REGEXP_COLORSTOP.test( imageData.args[0] );

    if (hasDirection) {
        imageData.args[0].split(/\s+/).reverse().forEach(function(position, index) {
            switch(position) {
            case "left":
                this.x0 = 0;
                this.x1 = 1;
                break;
            case "top":
                this.y0 = 0;
                this.y1 = 1;
                break;
            case "right":
                this.x0 = 1;
                this.x1 = 0;
                break;
            case "bottom":
                this.y0 = 1;
                this.y1 = 0;
                break;
            case "to":
                var y0 = this.y0;
                var x0 = this.x0;
                this.y0 = this.y1;
                this.x0 = this.x1;
                this.x1 = x0;
                this.y1 = y0;
                break;
            case "center":
                break; // centered by default
            // Firefox internally converts position keywords to percentages:
            // http://www.w3.org/TR/2010/WD-CSS2-20101207/colors.html#propdef-background-position
            default: // percentage or absolute length
                // TODO: support absolute start point positions (e.g., use bounds to convert px to a ratio)
                var ratio = parseFloat(position, 10) * 1e-2;
                if (isNaN(ratio)) { // invalid or unhandled value
                    break;
                }
                if (index === 0) {
                    this.y0 = ratio;
                    this.y1 = 1 - this.y0;
                } else {
                    this.x0 = ratio;
                    this.x1 = 1 - this.x0;
                }
                break;
            }
        }, this);
    } else {
        this.y0 = 0;
        this.y1 = 1;
    }

    this.colorStops = imageData.args.slice(hasDirection ? 1 : 0).map(function(colorStop) {
        var colorStopMatch = colorStop.match(GradientContainer.REGEXP_COLORSTOP);
        var value = +colorStopMatch[2];
        var unit = value === 0 ? "%" : colorStopMatch[3]; // treat "0" as "0%"
        return {
            color: new Color(colorStopMatch[1]),
            // TODO: support absolute stop positions (e.g., compute gradient line length & convert px to ratio)
            stop: unit === "%" ? value / 100 : null
        };
    });

    if (this.colorStops[0].stop === null) {
        this.colorStops[0].stop = 0;
    }

    if (this.colorStops[this.colorStops.length - 1].stop === null) {
        this.colorStops[this.colorStops.length - 1].stop = 1;
    }

    // calculates and fills-in explicit stop positions when omitted from rule
    this.colorStops.forEach(function(colorStop, index) {
        if (colorStop.stop === null) {
            this.colorStops.slice(index).some(function(find, count) {
                if (find.stop !== null) {
                    colorStop.stop = ((find.stop - this.colorStops[index - 1].stop) / (count + 1)) + this.colorStops[index - 1].stop;
                    return true;
                } else {
                    return false;
                }
            }, this);
        }
    }, this);
}

LinearGradientContainer.prototype = Object.create(GradientContainer.prototype);

// TODO: support <angle> (e.g. -?\d{1,3}(?:\.\d+)deg, etc. : https://developer.mozilla.org/docs/Web/CSS/angle )
LinearGradientContainer.REGEXP_DIRECTION = /^\s*(?:to|left|right|top|bottom|center|\d{1,3}(?:\.\d+)?%?)(?:\s|$)/i;

module.exports = LinearGradientContainer;

},{"./color":3,"./gradientcontainer":9}],13:[function(_dereq_,module,exports){
var logger = function() {
    if (logger.options.logging && window.console && window.console.log) {
        Function.prototype.bind.call(window.console.log, (window.console)).apply(window.console, [(Date.now() - logger.options.start) + "ms", "html2canvas:"].concat([].slice.call(arguments, 0)));
    }
};

logger.options = {logging: false};
module.exports = logger;

},{}],14:[function(_dereq_,module,exports){
var Color = _dereq_('./color');
var utils = _dereq_('./utils');
var getBounds = utils.getBounds;
var parseBackgrounds = utils.parseBackgrounds;
var offsetBounds = utils.offsetBounds;

function NodeContainer(node, parent) {
    this.node = node;
    this.parent = parent;
    this.stack = null;
    this.bounds = null;
    this.borders = null;
    this.clip = [];
    this.backgroundClip = [];
    this.offsetBounds = null;
    this.visible = null;
    this.computedStyles = null;
    this.colors = {};
    this.styles = {};
    this.backgroundImages = null;
    this.transformData = null;
    this.transformMatrix = null;
    this.isPseudoElement = false;
    this.opacity = null;
}

NodeContainer.prototype.cloneTo = function(stack) {
    stack.visible = this.visible;
    stack.borders = this.borders;
    stack.bounds = this.bounds;
    stack.clip = this.clip;
    stack.backgroundClip = this.backgroundClip;
    stack.computedStyles = this.computedStyles;
    stack.styles = this.styles;
    stack.backgroundImages = this.backgroundImages;
    stack.opacity = this.opacity;
};

NodeContainer.prototype.getOpacity = function() {
    return this.opacity === null ? (this.opacity = this.cssFloat('opacity')) : this.opacity;
};

NodeContainer.prototype.assignStack = function(stack) {
    this.stack = stack;
    stack.children.push(this);
};

NodeContainer.prototype.isElementVisible = function() {
    return this.node.nodeType === Node.TEXT_NODE ? this.parent.visible : (
        this.css('display') !== "none" &&
        this.css('visibility') !== "hidden" &&
        !this.node.hasAttribute("data-html2canvas-ignore") &&
        (this.node.nodeName !== "INPUT" || this.node.getAttribute("type") !== "hidden")
    );
};

NodeContainer.prototype.css = function(attribute) {
    if (!this.computedStyles) {
        this.computedStyles = this.isPseudoElement ? this.parent.computedStyle(this.before ? ":before" : ":after") : this.computedStyle(null);
    }

    return this.styles[attribute] || (this.styles[attribute] = this.computedStyles[attribute]);
};

NodeContainer.prototype.prefixedCss = function(attribute) {
    var prefixes = ["webkit", "moz", "ms", "o"];
    var value = this.css(attribute);
    if (value === undefined) {
        prefixes.some(function(prefix) {
            value = this.css(prefix + attribute.substr(0, 1).toUpperCase() + attribute.substr(1));
            return value !== undefined;
        }, this);
    }
    return value === undefined ? null : value;
};

NodeContainer.prototype.computedStyle = function(type) {
    return this.node.ownerDocument.defaultView.getComputedStyle(this.node, type);
};

NodeContainer.prototype.cssInt = function(attribute) {
    var value = parseInt(this.css(attribute), 10);
    return (isNaN(value)) ? 0 : value; // borders in old IE are throwing 'medium' for demo.html
};

NodeContainer.prototype.color = function(attribute) {
    return this.colors[attribute] || (this.colors[attribute] = new Color(this.css(attribute)));
};

NodeContainer.prototype.cssFloat = function(attribute) {
    var value = parseFloat(this.css(attribute));
    return (isNaN(value)) ? 0 : value;
};

NodeContainer.prototype.fontWeight = function() {
    var weight = this.css("fontWeight");
    switch(parseInt(weight, 10)){
    case 401:
        weight = "bold";
        break;
    case 400:
        weight = "normal";
        break;
    }
    return weight;
};

NodeContainer.prototype.parseClip = function() {
    var matches = this.css('clip').match(this.CLIP);
    if (matches) {
        return {
            top: parseInt(matches[1], 10),
            right: parseInt(matches[2], 10),
            bottom: parseInt(matches[3], 10),
            left: parseInt(matches[4], 10)
        };
    }
    return null;
};

NodeContainer.prototype.parseBackgroundImages = function() {
    return this.backgroundImages || (this.backgroundImages = parseBackgrounds(this.css("backgroundImage")));
};

NodeContainer.prototype.cssList = function(property, index) {
    var value = (this.css(property) || '').split(',');
    value = value[index || 0] || value[0] || 'auto';
    value = value.trim().split(' ');
    if (value.length === 1) {
        value = [value[0], isPercentage(value[0]) ? 'auto' : value[0]];
    }
    return value;
};

NodeContainer.prototype.parseBackgroundSize = function(bounds, image, index) {
    var size = this.cssList("backgroundSize", index);
    var width, height;

    if (isPercentage(size[0])) {
        width = bounds.width * parseFloat(size[0]) / 100;
    } else if (/contain|cover/.test(size[0])) {
        var targetRatio = bounds.width / bounds.height, currentRatio = image.width / image.height;
        return (targetRatio < currentRatio ^ size[0] === 'contain') ?  {width: bounds.height * currentRatio, height: bounds.height} : {width: bounds.width, height: bounds.width / currentRatio};
    } else {
        width = parseInt(size[0], 10);
    }

    if (size[0] === 'auto' && size[1] === 'auto') {
        height = image.height;
    } else if (size[1] === 'auto') {
        height = width / image.width * image.height;
    } else if (isPercentage(size[1])) {
        height =  bounds.height * parseFloat(size[1]) / 100;
    } else {
        height = parseInt(size[1], 10);
    }

    if (size[0] === 'auto') {
        width = height / image.height * image.width;
    }

    return {width: width, height: height};
};

NodeContainer.prototype.parseBackgroundPosition = function(bounds, image, index, backgroundSize) {
    var position = this.cssList('backgroundPosition', index);
    var left, top;

    if (isPercentage(position[0])){
        left = (bounds.width - (backgroundSize || image).width) * (parseFloat(position[0]) / 100);
    } else {
        left = parseInt(position[0], 10);
    }

    if (position[1] === 'auto') {
        top = left / image.width * image.height;
    } else if (isPercentage(position[1])){
        top =  (bounds.height - (backgroundSize || image).height) * parseFloat(position[1]) / 100;
    } else {
        top = parseInt(position[1], 10);
    }

    if (position[0] === 'auto') {
        left = top / image.height * image.width;
    }

    return {left: left, top: top};
};

NodeContainer.prototype.parseBackgroundRepeat = function(index) {
    return this.cssList("backgroundRepeat", index)[0];
};

NodeContainer.prototype.parseTextShadows = function() {
    var textShadow = this.css("textShadow");
    var results = [];

    if (textShadow && textShadow !== 'none') {
        var shadows = textShadow.match(this.TEXT_SHADOW_PROPERTY);
        for (var i = 0; shadows && (i < shadows.length); i++) {
            var s = shadows[i].match(this.TEXT_SHADOW_VALUES);
            results.push({
                color: new Color(s[0]),
                offsetX: s[1] ? parseFloat(s[1].replace('px', '')) : 0,
                offsetY: s[2] ? parseFloat(s[2].replace('px', '')) : 0,
                blur: s[3] ? s[3].replace('px', '') : 0
            });
        }
    }
    return results;
};

NodeContainer.prototype.parseTransform = function() {
    if (!this.transformData) {
        if (this.hasTransform()) {
            var offset = this.parseBounds();
            var origin = this.prefixedCss("transformOrigin").split(" ").map(removePx).map(asFloat);
            origin[0] += offset.left;
            origin[1] += offset.top;
            this.transformData = {
                origin: origin,
                matrix: this.parseTransformMatrix()
            };
        } else {
            this.transformData = {
                origin: [0, 0],
                matrix: [1, 0, 0, 1, 0, 0]
            };
        }
    }
    return this.transformData;
};

NodeContainer.prototype.parseTransformMatrix = function() {
    if (!this.transformMatrix) {
        var transform = this.prefixedCss("transform");
        var matrix = transform ? parseMatrix(transform.match(this.MATRIX_PROPERTY)) : null;
        this.transformMatrix = matrix ? matrix : [1, 0, 0, 1, 0, 0];
    }
    return this.transformMatrix;
};

NodeContainer.prototype.parseBounds = function() {
    return this.bounds || (this.bounds = this.hasTransform() ? offsetBounds(this.node) : getBounds(this.node));
};

NodeContainer.prototype.hasTransform = function() {
    return this.parseTransformMatrix().join(",") !== "1,0,0,1,0,0" || (this.parent && this.parent.hasTransform());
};

NodeContainer.prototype.getValue = function() {
    var value = this.node.value || "";
    if (this.node.tagName === "SELECT") {
        value = selectionValue(this.node);
    } else if (this.node.type === "password") {
        value = Array(value.length + 1).join('\u2022'); // jshint ignore:line
    }
    return value.length === 0 ? (this.node.placeholder || "") : value;
};

NodeContainer.prototype.MATRIX_PROPERTY = /(matrix|matrix3d)\((.+)\)/;
NodeContainer.prototype.TEXT_SHADOW_PROPERTY = /((rgba|rgb)\([^\)]+\)(\s-?\d+px){0,})/g;
NodeContainer.prototype.TEXT_SHADOW_VALUES = /(-?\d+px)|(#.+)|(rgb\(.+\))|(rgba\(.+\))/g;
NodeContainer.prototype.CLIP = /^rect\((\d+)px,? (\d+)px,? (\d+)px,? (\d+)px\)$/;

function selectionValue(node) {
    var option = node.options[node.selectedIndex || 0];
    return option ? (option.text || "") : "";
}

function parseMatrix(match) {
    if (match && match[1] === "matrix") {
        return match[2].split(",").map(function(s) {
            return parseFloat(s.trim());
        });
    } else if (match && match[1] === "matrix3d") {
        var matrix3d = match[2].split(",").map(function(s) {
          return parseFloat(s.trim());
        });
        return [matrix3d[0], matrix3d[1], matrix3d[4], matrix3d[5], matrix3d[12], matrix3d[13]];
    }
}

function isPercentage(value) {
    return value.toString().indexOf("%") !== -1;
}

function removePx(str) {
    return str.replace("px", "");
}

function asFloat(str) {
    return parseFloat(str);
}

module.exports = NodeContainer;

},{"./color":3,"./utils":26}],15:[function(_dereq_,module,exports){
var log = _dereq_('./log');
var punycode = _dereq_('punycode');
var NodeContainer = _dereq_('./nodecontainer');
var TextContainer = _dereq_('./textcontainer');
var PseudoElementContainer = _dereq_('./pseudoelementcontainer');
var FontMetrics = _dereq_('./fontmetrics');
var Color = _dereq_('./color');
var StackingContext = _dereq_('./stackingcontext');
var utils = _dereq_('./utils');
var bind = utils.bind;
var getBounds = utils.getBounds;
var parseBackgrounds = utils.parseBackgrounds;
var offsetBounds = utils.offsetBounds;

function NodeParser(element, renderer, support, imageLoader, options) {
    log("Starting NodeParser");
    this.renderer = renderer;
    this.options = options;
    this.range = null;
    this.support = support;
    this.renderQueue = [];
    this.stack = new StackingContext(true, 1, element.ownerDocument, null);
    var parent = new NodeContainer(element, null);
    if (options.background) {
        renderer.rectangle(0, 0, renderer.width, renderer.height, new Color(options.background));
    }
    if (element === element.ownerDocument.documentElement) {
        // http://www.w3.org/TR/css3-background/#special-backgrounds
        var canvasBackground = new NodeContainer(parent.color('backgroundColor').isTransparent() ? element.ownerDocument.body : element.ownerDocument.documentElement, null);
        renderer.rectangle(0, 0, renderer.width, renderer.height, canvasBackground.color('backgroundColor'));
    }
    parent.visibile = parent.isElementVisible();
    this.createPseudoHideStyles(element.ownerDocument);
    this.disableAnimations(element.ownerDocument);
    this.nodes = flatten([parent].concat(this.getChildren(parent)).filter(function(container) {
        return container.visible = container.isElementVisible();
    }).map(this.getPseudoElements, this));
    this.fontMetrics = new FontMetrics();
    log("Fetched nodes, total:", this.nodes.length);
    log("Calculate overflow clips");
    this.calculateOverflowClips();
    log("Start fetching images");
    this.images = imageLoader.fetch(this.nodes.filter(isElement));
    this.ready = this.images.ready.then(bind(function() {
        log("Images loaded, starting parsing");
        log("Creating stacking contexts");
        this.createStackingContexts();
        log("Sorting stacking contexts");
        this.sortStackingContexts(this.stack);
        this.parse(this.stack);
        log("Render queue created with " + this.renderQueue.length + " items");
        return new Promise(bind(function(resolve) {
            if (!options.async) {
                this.renderQueue.forEach(this.paint, this);
                resolve();
            } else if (typeof(options.async) === "function") {
                options.async.call(this, this.renderQueue, resolve);
            } else if (this.renderQueue.length > 0){
                this.renderIndex = 0;
                this.asyncRenderer(this.renderQueue, resolve);
            } else {
                resolve();
            }
        }, this));
    }, this));
}

NodeParser.prototype.calculateOverflowClips = function() {
    this.nodes.forEach(function(container) {
        if (isElement(container)) {
            if (isPseudoElement(container)) {
                container.appendToDOM();
            }
            container.borders = this.parseBorders(container);
            var clip = (container.css('overflow') === "hidden") ? [container.borders.clip] : [];
            var cssClip = container.parseClip();
            if (cssClip && ["absolute", "fixed"].indexOf(container.css('position')) !== -1) {
                clip.push([["rect",
                        container.bounds.left + cssClip.left,
                        container.bounds.top + cssClip.top,
                        cssClip.right - cssClip.left,
                        cssClip.bottom - cssClip.top
                ]]);
            }
            container.clip = hasParentClip(container) ? container.parent.clip.concat(clip) : clip;
            container.backgroundClip = (container.css('overflow') !== "hidden") ? container.clip.concat([container.borders.clip]) : container.clip;
            if (isPseudoElement(container)) {
                container.cleanDOM();
            }
        } else if (isTextNode(container)) {
            container.clip = hasParentClip(container) ? container.parent.clip : [];
        }
        if (!isPseudoElement(container)) {
            container.bounds = null;
        }
    }, this);
};

function hasParentClip(container) {
    return container.parent && container.parent.clip.length;
}

NodeParser.prototype.asyncRenderer = function(queue, resolve, asyncTimer) {
    asyncTimer = asyncTimer || Date.now();
    this.paint(queue[this.renderIndex++]);
    if (queue.length === this.renderIndex) {
        resolve();
    } else if (asyncTimer + 20 > Date.now()) {
        this.asyncRenderer(queue, resolve, asyncTimer);
    } else {
        setTimeout(bind(function() {
            this.asyncRenderer(queue, resolve);
        }, this), 0);
    }
};

NodeParser.prototype.createPseudoHideStyles = function(document) {
    this.createStyles(document, '.' + PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_BEFORE + ':before { content: "" !important; display: none !important; }' +
        '.' + PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_AFTER + ':after { content: "" !important; display: none !important; }');
};

NodeParser.prototype.disableAnimations = function(document) {
    this.createStyles(document, '* { -webkit-animation: none !important; -moz-animation: none !important; -o-animation: none !important; animation: none !important; ' +
        '-webkit-transition: none !important; -moz-transition: none !important; -o-transition: none !important; transition: none !important;}');
};

NodeParser.prototype.createStyles = function(document, styles) {
    var hidePseudoElements = document.createElement('style');
    hidePseudoElements.innerHTML = styles;
    document.body.appendChild(hidePseudoElements);
};

NodeParser.prototype.getPseudoElements = function(container) {
    var nodes = [[container]];
    if (container.node.nodeType === Node.ELEMENT_NODE) {
        var before = this.getPseudoElement(container, ":before");
        var after = this.getPseudoElement(container, ":after");

        if (before) {
            nodes.push(before);
        }

        if (after) {
            nodes.push(after);
        }
    }
    return flatten(nodes);
};

function toCamelCase(str) {
    return str.replace(/(\-[a-z])/g, function(match){
        return match.toUpperCase().replace('-','');
    });
}

NodeParser.prototype.getPseudoElement = function(container, type) {
    var style = container.computedStyle(type);
    if(!style || !style.content || style.content === "none" || style.content === "-moz-alt-content" || style.display === "none") {
        return null;
    }

    var content = stripQuotes(style.content);
    var isImage = content.substr(0, 3) === 'url';
    var pseudoNode = document.createElement(isImage ? 'img' : 'html2canvaspseudoelement');
    var pseudoContainer = new PseudoElementContainer(pseudoNode, container, type);

    for (var i = style.length-1; i >= 0; i--) {
        var property = toCamelCase(style.item(i));
        pseudoNode.style[property] = style[property];
    }

    pseudoNode.className = PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_BEFORE + " " + PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_AFTER;

    if (isImage) {
        pseudoNode.src = parseBackgrounds(content)[0].args[0];
        return [pseudoContainer];
    } else {
        var text = document.createTextNode(content);
        pseudoNode.appendChild(text);
        return [pseudoContainer, new TextContainer(text, pseudoContainer)];
    }
};


NodeParser.prototype.getChildren = function(parentContainer) {
    return flatten([].filter.call(parentContainer.node.childNodes, renderableNode).map(function(node) {
        var container = [node.nodeType === Node.TEXT_NODE ? new TextContainer(node, parentContainer) : new NodeContainer(node, parentContainer)].filter(nonIgnoredElement);
        return node.nodeType === Node.ELEMENT_NODE && container.length && node.tagName !== "TEXTAREA" ? (container[0].isElementVisible() ? container.concat(this.getChildren(container[0])) : []) : container;
    }, this));
};

NodeParser.prototype.newStackingContext = function(container, hasOwnStacking) {
    var stack = new StackingContext(hasOwnStacking, container.getOpacity(), container.node, container.parent);
    container.cloneTo(stack);
    var parentStack = hasOwnStacking ? stack.getParentStack(this) : stack.parent.stack;
    parentStack.contexts.push(stack);
    container.stack = stack;
};

NodeParser.prototype.createStackingContexts = function() {
    this.nodes.forEach(function(container) {
        if (isElement(container) && (this.isRootElement(container) || hasOpacity(container) || isPositionedForStacking(container) || this.isBodyWithTransparentRoot(container) || container.hasTransform())) {
            this.newStackingContext(container, true);
        } else if (isElement(container) && ((isPositioned(container) && zIndex0(container)) || isInlineBlock(container) || isFloating(container))) {
            this.newStackingContext(container, false);
        } else {
            container.assignStack(container.parent.stack);
        }
    }, this);
};

NodeParser.prototype.isBodyWithTransparentRoot = function(container) {
    return container.node.nodeName === "BODY" && container.parent.color('backgroundColor').isTransparent();
};

NodeParser.prototype.isRootElement = function(container) {
    return container.parent === null;
};

NodeParser.prototype.sortStackingContexts = function(stack) {
    stack.contexts.sort(zIndexSort(stack.contexts.slice(0)));
    stack.contexts.forEach(this.sortStackingContexts, this);
};

NodeParser.prototype.parseTextBounds = function(container) {
    return function(text, index, textList) {
        if (container.parent.css("textDecoration").substr(0, 4) !== "none" || text.trim().length !== 0) {
            if (this.support.rangeBounds && !container.parent.hasTransform()) {
                var offset = textList.slice(0, index).join("").length;
                return this.getRangeBounds(container.node, offset, text.length);
            } else if (container.node && typeof(container.node.data) === "string") {
                var replacementNode = container.node.splitText(text.length);
                var bounds = this.getWrapperBounds(container.node, container.parent.hasTransform());
                container.node = replacementNode;
                return bounds;
            }
        } else if(!this.support.rangeBounds || container.parent.hasTransform()){
            container.node = container.node.splitText(text.length);
        }
        return {};
    };
};

NodeParser.prototype.getWrapperBounds = function(node, transform) {
    var wrapper = node.ownerDocument.createElement('html2canvaswrapper');
    var parent = node.parentNode,
        backupText = node.cloneNode(true);

    wrapper.appendChild(node.cloneNode(true));
    parent.replaceChild(wrapper, node);
    var bounds = transform ? offsetBounds(wrapper) : getBounds(wrapper);
    parent.replaceChild(backupText, wrapper);
    return bounds;
};

NodeParser.prototype.getRangeBounds = function(node, offset, length) {
    var range = this.range || (this.range = node.ownerDocument.createRange());
    range.setStart(node, offset);
    range.setEnd(node, offset + length);
    return range.getBoundingClientRect();
};

function ClearTransform() {}

NodeParser.prototype.parse = function(stack) {
    // http://www.w3.org/TR/CSS21/visuren.html#z-index
    var negativeZindex = stack.contexts.filter(negativeZIndex); // 2. the child stacking contexts with negative stack levels (most negative first).
    var descendantElements = stack.children.filter(isElement);
    var descendantNonFloats = descendantElements.filter(not(isFloating));
    var nonInlineNonPositionedDescendants = descendantNonFloats.filter(not(isPositioned)).filter(not(inlineLevel)); // 3 the in-flow, non-inline-level, non-positioned descendants.
    var nonPositionedFloats = descendantElements.filter(not(isPositioned)).filter(isFloating); // 4. the non-positioned floats.
    var inFlow = descendantNonFloats.filter(not(isPositioned)).filter(inlineLevel); // 5. the in-flow, inline-level, non-positioned descendants, including inline tables and inline blocks.
    var stackLevel0 = stack.contexts.concat(descendantNonFloats.filter(isPositioned)).filter(zIndex0); // 6. the child stacking contexts with stack level 0 and the positioned descendants with stack level 0.
    var text = stack.children.filter(isTextNode).filter(hasText);
    var positiveZindex = stack.contexts.filter(positiveZIndex); // 7. the child stacking contexts with positive stack levels (least positive first).
    negativeZindex.concat(nonInlineNonPositionedDescendants).concat(nonPositionedFloats)
        .concat(inFlow).concat(stackLevel0).concat(text).concat(positiveZindex).forEach(function(container) {
            this.renderQueue.push(container);
            if (isStackingContext(container)) {
                this.parse(container);
                this.renderQueue.push(new ClearTransform());
            }
        }, this);
};

NodeParser.prototype.paint = function(container) {
    try {
        if (container instanceof ClearTransform) {
            this.renderer.ctx.restore();
        } else if (isTextNode(container)) {
            if (isPseudoElement(container.parent)) {
                container.parent.appendToDOM();
            }
            this.paintText(container);
            if (isPseudoElement(container.parent)) {
                container.parent.cleanDOM();
            }
        } else {
            this.paintNode(container);
        }
    } catch(e) {
        log(e);
        if (this.options.strict) {
            throw e;
        }
    }
};

NodeParser.prototype.paintNode = function(container) {
    if (isStackingContext(container)) {
        this.renderer.setOpacity(container.opacity);
        this.renderer.ctx.save();
        if (container.hasTransform()) {
            this.renderer.setTransform(container.parseTransform());
        }
    }

    if (container.node.nodeName === "INPUT" && container.node.type === "checkbox") {
        this.paintCheckbox(container);
    } else if (container.node.nodeName === "INPUT" && container.node.type === "radio") {
        this.paintRadio(container);
    } else {
        this.paintElement(container);
    }
};

NodeParser.prototype.paintElement = function(container) {
    var bounds = container.parseBounds();
    this.renderer.clip(container.backgroundClip, function() {
        this.renderer.renderBackground(container, bounds, container.borders.borders.map(getWidth));
    }, this);

    this.renderer.clip(container.clip, function() {
        this.renderer.renderBorders(container.borders.borders);
    }, this);

    this.renderer.clip(container.backgroundClip, function() {
        switch (container.node.nodeName) {
        case "svg":
        case "IFRAME":
            var imgContainer = this.images.get(container.node);
            if (imgContainer) {
                this.renderer.renderImage(container, bounds, container.borders, imgContainer);
            } else {
                log("Error loading <" + container.node.nodeName + ">", container.node);
            }
            break;
        case "IMG":
            var imageContainer = this.images.get(container.node.src);
            if (imageContainer) {
                this.renderer.renderImage(container, bounds, container.borders, imageContainer);
            } else {
                log("Error loading <img>", container.node.src);
            }
            break;
        case "CANVAS":
            this.renderer.renderImage(container, bounds, container.borders, {image: container.node});
            break;
        case "SELECT":
        case "INPUT":
        case "TEXTAREA":
            this.paintFormValue(container);
            break;
        }
    }, this);
};

NodeParser.prototype.paintCheckbox = function(container) {
    var b = container.parseBounds();

    var size = Math.min(b.width, b.height);
    var bounds = {width: size - 1, height: size - 1, top: b.top, left: b.left};
    var r = [3, 3];
    var radius = [r, r, r, r];
    var borders = [1,1,1,1].map(function(w) {
        return {color: new Color('#A5A5A5'), width: w};
    });

    var borderPoints = calculateCurvePoints(bounds, radius, borders);

    this.renderer.clip(container.backgroundClip, function() {
        this.renderer.rectangle(bounds.left + 1, bounds.top + 1, bounds.width - 2, bounds.height - 2, new Color("#DEDEDE"));
        this.renderer.renderBorders(calculateBorders(borders, bounds, borderPoints, radius));
        if (container.node.checked) {
            this.renderer.font(new Color('#424242'), 'normal', 'normal', 'bold', (size - 3) + "px", 'arial');
            this.renderer.text("\u2714", bounds.left + size / 6, bounds.top + size - 1);
        }
    }, this);
};

NodeParser.prototype.paintRadio = function(container) {
    var bounds = container.parseBounds();

    var size = Math.min(bounds.width, bounds.height) - 2;

    this.renderer.clip(container.backgroundClip, function() {
        this.renderer.circleStroke(bounds.left + 1, bounds.top + 1, size, new Color('#DEDEDE'), 1, new Color('#A5A5A5'));
        if (container.node.checked) {
            this.renderer.circle(Math.ceil(bounds.left + size / 4) + 1, Math.ceil(bounds.top + size / 4) + 1, Math.floor(size / 2), new Color('#424242'));
        }
    }, this);
};

NodeParser.prototype.paintFormValue = function(container) {
    var value = container.getValue();
    if (value.length > 0) {
        var document = container.node.ownerDocument;
        var wrapper = document.createElement('html2canvaswrapper');
        var properties = ['lineHeight', 'textAlign', 'fontFamily', 'fontWeight', 'fontSize', 'color',
            'paddingLeft', 'paddingTop', 'paddingRight', 'paddingBottom',
            'width', 'height', 'borderLeftStyle', 'borderTopStyle', 'borderLeftWidth', 'borderTopWidth',
            'boxSizing', 'whiteSpace', 'wordWrap'];

        properties.forEach(function(property) {
            try {
                wrapper.style[property] = container.css(property);
            } catch(e) {
                // Older IE has issues with "border"
                log("html2canvas: Parse: Exception caught in renderFormValue: " + e.message);
            }
        });
        var bounds = container.parseBounds();
        wrapper.style.position = "fixed";
        wrapper.style.left = bounds.left + "px";
        wrapper.style.top = bounds.top + "px";
        wrapper.textContent = value;
        document.body.appendChild(wrapper);
        this.paintText(new TextContainer(wrapper.firstChild, container));
        document.body.removeChild(wrapper);
    }
};

NodeParser.prototype.paintText = function(container) {
    container.applyTextTransform();
    var characters = punycode.ucs2.decode(container.node.data);
    var textList = (!this.options.letterRendering || noLetterSpacing(container)) && !hasUnicode(container.node.data) ? getWords(characters) : characters.map(function(character) {
        return punycode.ucs2.encode([character]);
    });

    var weight = container.parent.fontWeight();
    var size = container.parent.css('fontSize');
    var family = container.parent.css('fontFamily');
    var shadows = container.parent.parseTextShadows();

    this.renderer.font(container.parent.color('color'), container.parent.css('fontStyle'), container.parent.css('fontVariant'), weight, size, family);
    if (shadows.length) {
        // TODO: support multiple text shadows
        this.renderer.fontShadow(shadows[0].color, shadows[0].offsetX, shadows[0].offsetY, shadows[0].blur);
    } else {
        this.renderer.clearShadow();
    }

    this.renderer.clip(container.parent.clip, function() {
        textList.map(this.parseTextBounds(container), this).forEach(function(bounds, index) {
            if (bounds) {
                this.renderer.text(textList[index], bounds.left, bounds.bottom);
                this.renderTextDecoration(container.parent, bounds, this.fontMetrics.getMetrics(family, size));
            }
        }, this);
    }, this);
};

NodeParser.prototype.renderTextDecoration = function(container, bounds, metrics) {
    switch(container.css("textDecoration").split(" ")[0]) {
    case "underline":
        // Draws a line at the baseline of the font
        // TODO As some browsers display the line as more than 1px if the font-size is big, need to take that into account both in position and size
        this.renderer.rectangle(bounds.left, Math.round(bounds.top + metrics.baseline + metrics.lineWidth), bounds.width, 1, container.color("color"));
        break;
    case "overline":
        this.renderer.rectangle(bounds.left, Math.round(bounds.top), bounds.width, 1, container.color("color"));
        break;
    case "line-through":
        // TODO try and find exact position for line-through
        this.renderer.rectangle(bounds.left, Math.ceil(bounds.top + metrics.middle + metrics.lineWidth), bounds.width, 1, container.color("color"));
        break;
    }
};

var borderColorTransforms = {
    inset: [
        ["darken", 0.60],
        ["darken", 0.10],
        ["darken", 0.10],
        ["darken", 0.60]
    ]
};

NodeParser.prototype.parseBorders = function(container) {
    var nodeBounds = container.parseBounds();
    var radius = getBorderRadiusData(container);
    var borders = ["Top", "Right", "Bottom", "Left"].map(function(side, index) {
        var style = container.css('border' + side + 'Style');
        var color = container.color('border' + side + 'Color');
        if (style === "inset" && color.isBlack()) {
            color = new Color([255, 255, 255, color.a]); // this is wrong, but
        }
        var colorTransform = borderColorTransforms[style] ? borderColorTransforms[style][index] : null;
        return {
            width: container.cssInt('border' + side + 'Width'),
            color: colorTransform ? color[colorTransform[0]](colorTransform[1]) : color,
            args: null
        };
    });
    var borderPoints = calculateCurvePoints(nodeBounds, radius, borders);

    return {
        clip: this.parseBackgroundClip(container, borderPoints, borders, radius, nodeBounds),
        borders: calculateBorders(borders, nodeBounds, borderPoints, radius)
    };
};

function calculateBorders(borders, nodeBounds, borderPoints, radius) {
    return borders.map(function(border, borderSide) {
        if (border.width > 0) {
            var bx = nodeBounds.left;
            var by = nodeBounds.top;
            var bw = nodeBounds.width;
            var bh = nodeBounds.height - (borders[2].width);

            switch(borderSide) {
            case 0:
                // top border
                bh = borders[0].width;
                border.args = drawSide({
                        c1: [bx, by],
                        c2: [bx + bw, by],
                        c3: [bx + bw - borders[1].width, by + bh],
                        c4: [bx + borders[3].width, by + bh]
                    }, radius[0], radius[1],
                    borderPoints.topLeftOuter, borderPoints.topLeftInner, borderPoints.topRightOuter, borderPoints.topRightInner);
                break;
            case 1:
                // right border
                bx = nodeBounds.left + nodeBounds.width - (borders[1].width);
                bw = borders[1].width;

                border.args = drawSide({
                        c1: [bx + bw, by],
                        c2: [bx + bw, by + bh + borders[2].width],
                        c3: [bx, by + bh],
                        c4: [bx, by + borders[0].width]
                    }, radius[1], radius[2],
                    borderPoints.topRightOuter, borderPoints.topRightInner, borderPoints.bottomRightOuter, borderPoints.bottomRightInner);
                break;
            case 2:
                // bottom border
                by = (by + nodeBounds.height) - (borders[2].width);
                bh = borders[2].width;
                border.args = drawSide({
                        c1: [bx + bw, by + bh],
                        c2: [bx, by + bh],
                        c3: [bx + borders[3].width, by],
                        c4: [bx + bw - borders[3].width, by]
                    }, radius[2], radius[3],
                    borderPoints.bottomRightOuter, borderPoints.bottomRightInner, borderPoints.bottomLeftOuter, borderPoints.bottomLeftInner);
                break;
            case 3:
                // left border
                bw = borders[3].width;
                border.args = drawSide({
                        c1: [bx, by + bh + borders[2].width],
                        c2: [bx, by],
                        c3: [bx + bw, by + borders[0].width],
                        c4: [bx + bw, by + bh]
                    }, radius[3], radius[0],
                    borderPoints.bottomLeftOuter, borderPoints.bottomLeftInner, borderPoints.topLeftOuter, borderPoints.topLeftInner);
                break;
            }
        }
        return border;
    });
}

NodeParser.prototype.parseBackgroundClip = function(container, borderPoints, borders, radius, bounds) {
    var backgroundClip = container.css('backgroundClip'),
        borderArgs = [];

    switch(backgroundClip) {
    case "content-box":
    case "padding-box":
        parseCorner(borderArgs, radius[0], radius[1], borderPoints.topLeftInner, borderPoints.topRightInner, bounds.left + borders[3].width, bounds.top + borders[0].width);
        parseCorner(borderArgs, radius[1], radius[2], borderPoints.topRightInner, borderPoints.bottomRightInner, bounds.left + bounds.width - borders[1].width, bounds.top + borders[0].width);
        parseCorner(borderArgs, radius[2], radius[3], borderPoints.bottomRightInner, borderPoints.bottomLeftInner, bounds.left + bounds.width - borders[1].width, bounds.top + bounds.height - borders[2].width);
        parseCorner(borderArgs, radius[3], radius[0], borderPoints.bottomLeftInner, borderPoints.topLeftInner, bounds.left + borders[3].width, bounds.top + bounds.height - borders[2].width);
        break;

    default:
        parseCorner(borderArgs, radius[0], radius[1], borderPoints.topLeftOuter, borderPoints.topRightOuter, bounds.left, bounds.top);
        parseCorner(borderArgs, radius[1], radius[2], borderPoints.topRightOuter, borderPoints.bottomRightOuter, bounds.left + bounds.width, bounds.top);
        parseCorner(borderArgs, radius[2], radius[3], borderPoints.bottomRightOuter, borderPoints.bottomLeftOuter, bounds.left + bounds.width, bounds.top + bounds.height);
        parseCorner(borderArgs, radius[3], radius[0], borderPoints.bottomLeftOuter, borderPoints.topLeftOuter, bounds.left, bounds.top + bounds.height);
        break;
    }

    return borderArgs;
};

function getCurvePoints(x, y, r1, r2) {
    var kappa = 4 * ((Math.sqrt(2) - 1) / 3);
    var ox = (r1) * kappa, // control point offset horizontal
        oy = (r2) * kappa, // control point offset vertical
        xm = x + r1, // x-middle
        ym = y + r2; // y-middle
    return {
        topLeft: bezierCurve({x: x, y: ym}, {x: x, y: ym - oy}, {x: xm - ox, y: y}, {x: xm, y: y}),
        topRight: bezierCurve({x: x, y: y}, {x: x + ox,y: y}, {x: xm, y: ym - oy}, {x: xm, y: ym}),
        bottomRight: bezierCurve({x: xm, y: y}, {x: xm, y: y + oy}, {x: x + ox, y: ym}, {x: x, y: ym}),
        bottomLeft: bezierCurve({x: xm, y: ym}, {x: xm - ox, y: ym}, {x: x, y: y + oy}, {x: x, y:y})
    };
}

function calculateCurvePoints(bounds, borderRadius, borders) {
    var x = bounds.left,
        y = bounds.top,
        width = bounds.width,
        height = bounds.height,

        tlh = borderRadius[0][0] < width / 2 ? borderRadius[0][0] : width / 2,
        tlv = borderRadius[0][1] < height / 2 ? borderRadius[0][1] : height / 2,
        trh = borderRadius[1][0] < width / 2 ? borderRadius[1][0] : width / 2,
        trv = borderRadius[1][1] < height / 2 ? borderRadius[1][1] : height / 2,
        brh = borderRadius[2][0] < width / 2 ? borderRadius[2][0] : width / 2,
        brv = borderRadius[2][1] < height / 2 ? borderRadius[2][1] : height / 2,
        blh = borderRadius[3][0] < width / 2 ? borderRadius[3][0] : width / 2,
        blv = borderRadius[3][1] < height / 2 ? borderRadius[3][1] : height / 2;

    var topWidth = width - trh,
        rightHeight = height - brv,
        bottomWidth = width - brh,
        leftHeight = height - blv;

    return {
        topLeftOuter: getCurvePoints(x, y, tlh, tlv).topLeft.subdivide(0.5),
        topLeftInner: getCurvePoints(x + borders[3].width, y + borders[0].width, Math.max(0, tlh - borders[3].width), Math.max(0, tlv - borders[0].width)).topLeft.subdivide(0.5),
        topRightOuter: getCurvePoints(x + topWidth, y, trh, trv).topRight.subdivide(0.5),
        topRightInner: getCurvePoints(x + Math.min(topWidth, width + borders[3].width), y + borders[0].width, (topWidth > width + borders[3].width) ? 0 :trh - borders[3].width, trv - borders[0].width).topRight.subdivide(0.5),
        bottomRightOuter: getCurvePoints(x + bottomWidth, y + rightHeight, brh, brv).bottomRight.subdivide(0.5),
        bottomRightInner: getCurvePoints(x + Math.min(bottomWidth, width - borders[3].width), y + Math.min(rightHeight, height + borders[0].width), Math.max(0, brh - borders[1].width),  brv - borders[2].width).bottomRight.subdivide(0.5),
        bottomLeftOuter: getCurvePoints(x, y + leftHeight, blh, blv).bottomLeft.subdivide(0.5),
        bottomLeftInner: getCurvePoints(x + borders[3].width, y + leftHeight, Math.max(0, blh - borders[3].width), blv - borders[2].width).bottomLeft.subdivide(0.5)
    };
}

function bezierCurve(start, startControl, endControl, end) {
    var lerp = function (a, b, t) {
        return {
            x: a.x + (b.x - a.x) * t,
            y: a.y + (b.y - a.y) * t
        };
    };

    return {
        start: start,
        startControl: startControl,
        endControl: endControl,
        end: end,
        subdivide: function(t) {
            var ab = lerp(start, startControl, t),
                bc = lerp(startControl, endControl, t),
                cd = lerp(endControl, end, t),
                abbc = lerp(ab, bc, t),
                bccd = lerp(bc, cd, t),
                dest = lerp(abbc, bccd, t);
            return [bezierCurve(start, ab, abbc, dest), bezierCurve(dest, bccd, cd, end)];
        },
        curveTo: function(borderArgs) {
            borderArgs.push(["bezierCurve", startControl.x, startControl.y, endControl.x, endControl.y, end.x, end.y]);
        },
        curveToReversed: function(borderArgs) {
            borderArgs.push(["bezierCurve", endControl.x, endControl.y, startControl.x, startControl.y, start.x, start.y]);
        }
    };
}

function drawSide(borderData, radius1, radius2, outer1, inner1, outer2, inner2) {
    var borderArgs = [];

    if (radius1[0] > 0 || radius1[1] > 0) {
        borderArgs.push(["line", outer1[1].start.x, outer1[1].start.y]);
        outer1[1].curveTo(borderArgs);
    } else {
        borderArgs.push([ "line", borderData.c1[0], borderData.c1[1]]);
    }

    if (radius2[0] > 0 || radius2[1] > 0) {
        borderArgs.push(["line", outer2[0].start.x, outer2[0].start.y]);
        outer2[0].curveTo(borderArgs);
        borderArgs.push(["line", inner2[0].end.x, inner2[0].end.y]);
        inner2[0].curveToReversed(borderArgs);
    } else {
        borderArgs.push(["line", borderData.c2[0], borderData.c2[1]]);
        borderArgs.push(["line", borderData.c3[0], borderData.c3[1]]);
    }

    if (radius1[0] > 0 || radius1[1] > 0) {
        borderArgs.push(["line", inner1[1].end.x, inner1[1].end.y]);
        inner1[1].curveToReversed(borderArgs);
    } else {
        borderArgs.push(["line", borderData.c4[0], borderData.c4[1]]);
    }

    return borderArgs;
}

function parseCorner(borderArgs, radius1, radius2, corner1, corner2, x, y) {
    if (radius1[0] > 0 || radius1[1] > 0) {
        borderArgs.push(["line", corner1[0].start.x, corner1[0].start.y]);
        corner1[0].curveTo(borderArgs);
        corner1[1].curveTo(borderArgs);
    } else {
        borderArgs.push(["line", x, y]);
    }

    if (radius2[0] > 0 || radius2[1] > 0) {
        borderArgs.push(["line", corner2[0].start.x, corner2[0].start.y]);
    }
}

function negativeZIndex(container) {
    return container.cssInt("zIndex") < 0;
}

function positiveZIndex(container) {
    return container.cssInt("zIndex") > 0;
}

function zIndex0(container) {
    return container.cssInt("zIndex") === 0;
}

function inlineLevel(container) {
    return ["inline", "inline-block", "inline-table"].indexOf(container.css("display")) !== -1;
}

function isStackingContext(container) {
    return (container instanceof StackingContext);
}

function hasText(container) {
    return container.node.data.trim().length > 0;
}

function noLetterSpacing(container) {
    return (/^(normal|none|0px)$/.test(container.parent.css("letterSpacing")));
}

function getBorderRadiusData(container) {
    return ["TopLeft", "TopRight", "BottomRight", "BottomLeft"].map(function(side) {
        var value = container.css('border' + side + 'Radius');
        var arr = value.split(" ");
        if (arr.length <= 1) {
            arr[1] = arr[0];
        }
        return arr.map(asInt);
    });
}

function renderableNode(node) {
    return (node.nodeType === Node.TEXT_NODE || node.nodeType === Node.ELEMENT_NODE);
}

function isPositionedForStacking(container) {
    var position = container.css("position");
    var zIndex = (["absolute", "relative", "fixed"].indexOf(position) !== -1) ? container.css("zIndex") : "auto";
    return zIndex !== "auto";
}

function isPositioned(container) {
    return container.css("position") !== "static";
}

function isFloating(container) {
    return container.css("float") !== "none";
}

function isInlineBlock(container) {
    return ["inline-block", "inline-table"].indexOf(container.css("display")) !== -1;
}

function not(callback) {
    var context = this;
    return function() {
        return !callback.apply(context, arguments);
    };
}

function isElement(container) {
    return container.node.nodeType === Node.ELEMENT_NODE;
}

function isPseudoElement(container) {
    return container.isPseudoElement === true;
}

function isTextNode(container) {
    return container.node.nodeType === Node.TEXT_NODE;
}

function zIndexSort(contexts) {
    return function(a, b) {
        return (a.cssInt("zIndex") + (contexts.indexOf(a) / contexts.length)) - (b.cssInt("zIndex") + (contexts.indexOf(b) / contexts.length));
    };
}

function hasOpacity(container) {
    return container.getOpacity() < 1;
}

function asInt(value) {
    return parseInt(value, 10);
}

function getWidth(border) {
    return border.width;
}

function nonIgnoredElement(nodeContainer) {
    return (nodeContainer.node.nodeType !== Node.ELEMENT_NODE || ["SCRIPT", "HEAD", "TITLE", "OBJECT", "BR", "OPTION"].indexOf(nodeContainer.node.nodeName) === -1);
}

function flatten(arrays) {
    return [].concat.apply([], arrays);
}

function stripQuotes(content) {
    var first = content.substr(0, 1);
    return (first === content.substr(content.length - 1) && first.match(/'|"/)) ? content.substr(1, content.length - 2) : content;
}

function getWords(characters) {
    var words = [], i = 0, onWordBoundary = false, word;
    while(characters.length) {
        if (isWordBoundary(characters[i]) === onWordBoundary) {
            word = characters.splice(0, i);
            if (word.length) {
                words.push(punycode.ucs2.encode(word));
            }
            onWordBoundary =! onWordBoundary;
            i = 0;
        } else {
            i++;
        }

        if (i >= characters.length) {
            word = characters.splice(0, i);
            if (word.length) {
                words.push(punycode.ucs2.encode(word));
            }
        }
    }
    return words;
}

function isWordBoundary(characterCode) {
    return [
        32, // <space>
        13, // \r
        10, // \n
        9, // \t
        45 // -
    ].indexOf(characterCode) !== -1;
}

function hasUnicode(string) {
    return (/[^\u0000-\u00ff]/).test(string);
}

module.exports = NodeParser;

},{"./color":3,"./fontmetrics":7,"./log":13,"./nodecontainer":14,"./pseudoelementcontainer":18,"./stackingcontext":21,"./textcontainer":25,"./utils":26,"punycode":1}],16:[function(_dereq_,module,exports){
var XHR = _dereq_('./xhr');
var utils = _dereq_('./utils');
var log = _dereq_('./log');
var createWindowClone = _dereq_('./clone');
var decode64 = utils.decode64;

function Proxy(src, proxyUrl, document) {
    var supportsCORS = ('withCredentials' in new XMLHttpRequest());
    if (!proxyUrl) {
        return Promise.reject("No proxy configured");
    }
    var callback = createCallback(supportsCORS);
    var url = createProxyUrl(proxyUrl, src, callback);

    return supportsCORS ? XHR(url) : (jsonp(document, url, callback).then(function(response) {
        return decode64(response.content);
    }));
}
var proxyCount = 0;

function ProxyURL(src, proxyUrl, document) {
    var supportsCORSImage = ('crossOrigin' in new Image());
    var callback = createCallback(supportsCORSImage);
    var url = createProxyUrl(proxyUrl, src, callback);
    return (supportsCORSImage ? Promise.resolve(url) : jsonp(document, url, callback).then(function(response) {
        return "data:" + response.type + ";base64," + response.content;
    }));
}

function jsonp(document, url, callback) {
    return new Promise(function(resolve, reject) {
        var s = document.createElement("script");
        var cleanup = function() {
            delete window.html2canvas.proxy[callback];
            document.body.removeChild(s);
        };
        window.html2canvas.proxy[callback] = function(response) {
            cleanup();
            resolve(response);
        };
        s.src = url;
        s.onerror = function(e) {
            cleanup();
            reject(e);
        };
        document.body.appendChild(s);
    });
}

function createCallback(useCORS) {
    return !useCORS ? "html2canvas_" + Date.now() + "_" + (++proxyCount) + "_" + Math.round(Math.random() * 100000) : "";
}

function createProxyUrl(proxyUrl, src, callback) {
    return proxyUrl + "?url=" + encodeURIComponent(src) + (callback.length ? "&callback=html2canvas.proxy." + callback : "");
}

function documentFromHTML(src) {
    return function(html) {
        var parser = new DOMParser(), doc;
        try {
            doc = parser.parseFromString(html, "text/html");
        } catch(e) {
            log("DOMParser not supported, falling back to createHTMLDocument");
            doc = document.implementation.createHTMLDocument("");
            try {
                doc.open();
                doc.write(html);
                doc.close();
            } catch(ee) {
                log("createHTMLDocument write not supported, falling back to document.body.innerHTML");
                doc.body.innerHTML = html; // ie9 doesnt support writing to documentElement
            }
        }

        var b = doc.querySelector("base");
        if (!b || !b.href.host) {
            var base = doc.createElement("base");
            base.href = src;
            doc.head.insertBefore(base, doc.head.firstChild);
        }

        return doc;
    };
}

function loadUrlDocument(src, proxy, document, width, height, options) {
    return new Proxy(src, proxy, window.document).then(documentFromHTML(src)).then(function(doc) {
        return createWindowClone(doc, document, width, height, options, 0, 0);
    });
}

exports.Proxy = Proxy;
exports.ProxyURL = ProxyURL;
exports.loadUrlDocument = loadUrlDocument;

},{"./clone":2,"./log":13,"./utils":26,"./xhr":28}],17:[function(_dereq_,module,exports){
var ProxyURL = _dereq_('./proxy').ProxyURL;

function ProxyImageContainer(src, proxy) {
    var link = document.createElement("a");
    link.href = src;
    src = link.href;
    this.src = src;
    this.image = new Image();
    var self = this;
    this.promise = new Promise(function(resolve, reject) {
        self.image.crossOrigin = "Anonymous";
        self.image.onload = resolve;
        self.image.onerror = reject;

        new ProxyURL(src, proxy, document).then(function(url) {
            self.image.src = url;
        })['catch'](reject);
    });
}

module.exports = ProxyImageContainer;

},{"./proxy":16}],18:[function(_dereq_,module,exports){
var NodeContainer = _dereq_('./nodecontainer');

function PseudoElementContainer(node, parent, type) {
    NodeContainer.call(this, node, parent);
    this.isPseudoElement = true;
    this.before = type === ":before";
}

PseudoElementContainer.prototype.cloneTo = function(stack) {
    PseudoElementContainer.prototype.cloneTo.call(this, stack);
    stack.isPseudoElement = true;
    stack.before = this.before;
};

PseudoElementContainer.prototype = Object.create(NodeContainer.prototype);

PseudoElementContainer.prototype.appendToDOM = function() {
    if (this.before) {
        this.parent.node.insertBefore(this.node, this.parent.node.firstChild);
    } else {
        this.parent.node.appendChild(this.node);
    }
    this.parent.node.className += " " + this.getHideClass();
};

PseudoElementContainer.prototype.cleanDOM = function() {
    this.node.parentNode.removeChild(this.node);
    this.parent.node.className = this.parent.node.className.replace(this.getHideClass(), "");
};

PseudoElementContainer.prototype.getHideClass = function() {
    return this["PSEUDO_HIDE_ELEMENT_CLASS_" + (this.before ? "BEFORE" : "AFTER")];
};

PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_BEFORE = "___html2canvas___pseudoelement_before";
PseudoElementContainer.prototype.PSEUDO_HIDE_ELEMENT_CLASS_AFTER = "___html2canvas___pseudoelement_after";

module.exports = PseudoElementContainer;

},{"./nodecontainer":14}],19:[function(_dereq_,module,exports){
var log = _dereq_('./log');

function Renderer(width, height, images, options, document) {
    this.width = width;
    this.height = height;
    this.images = images;
    this.options = options;
    this.document = document;
}

Renderer.prototype.renderImage = function(container, bounds, borderData, imageContainer) {
    var paddingLeft = container.cssInt('paddingLeft'),
        paddingTop = container.cssInt('paddingTop'),
        paddingRight = container.cssInt('paddingRight'),
        paddingBottom = container.cssInt('paddingBottom'),
        borders = borderData.borders;

    var width = bounds.width - (borders[1].width + borders[3].width + paddingLeft + paddingRight);
    var height = bounds.height - (borders[0].width + borders[2].width + paddingTop + paddingBottom);
    this.drawImage(
        imageContainer,
        0,
        0,
        imageContainer.image.width || width,
        imageContainer.image.height || height,
        bounds.left + paddingLeft + borders[3].width,
        bounds.top + paddingTop + borders[0].width,
        width,
        height
    );
};

Renderer.prototype.renderBackground = function(container, bounds, borderData) {
    if (bounds.height > 0 && bounds.width > 0) {
        this.renderBackgroundColor(container, bounds);
        this.renderBackgroundImage(container, bounds, borderData);
    }
};

Renderer.prototype.renderBackgroundColor = function(container, bounds) {
    var color = container.color("backgroundColor");
    if (!color.isTransparent()) {
        this.rectangle(bounds.left, bounds.top, bounds.width, bounds.height, color);
    }
};

Renderer.prototype.renderBorders = function(borders) {
    borders.forEach(this.renderBorder, this);
};

Renderer.prototype.renderBorder = function(data) {
    if (!data.color.isTransparent() && data.args !== null) {
        this.drawShape(data.args, data.color);
    }
};

Renderer.prototype.renderBackgroundImage = function(container, bounds, borderData) {
    var backgroundImages = container.parseBackgroundImages();
    backgroundImages.reverse().forEach(function(backgroundImage, index, arr) {
        switch(backgroundImage.method) {
        case "url":
            var image = this.images.get(backgroundImage.args[0]);
            if (image) {
                this.renderBackgroundRepeating(container, bounds, image, arr.length - (index+1), borderData);
            } else {
                log("Error loading background-image", backgroundImage.args[0]);
            }
            break;
        case "linear-gradient":
        case "gradient":
            var gradientImage = this.images.get(backgroundImage.value);
            if (gradientImage) {
                this.renderBackgroundGradient(gradientImage, bounds, borderData);
            } else {
                log("Error loading background-image", backgroundImage.args[0]);
            }
            break;
        case "none":
            break;
        default:
            log("Unknown background-image type", backgroundImage.args[0]);
        }
    }, this);
};

Renderer.prototype.renderBackgroundRepeating = function(container, bounds, imageContainer, index, borderData) {
    var size = container.parseBackgroundSize(bounds, imageContainer.image, index);
    var position = container.parseBackgroundPosition(bounds, imageContainer.image, index, size);
    var repeat = container.parseBackgroundRepeat(index);
    switch (repeat) {
    case "repeat-x":
    case "repeat no-repeat":
        this.backgroundRepeatShape(imageContainer, position, size, bounds, bounds.left + borderData[3], bounds.top + position.top + borderData[0], 99999, size.height, borderData);
        break;
    case "repeat-y":
    case "no-repeat repeat":
        this.backgroundRepeatShape(imageContainer, position, size, bounds, bounds.left + position.left + borderData[3], bounds.top + borderData[0], size.width, 99999, borderData);
        break;
    case "no-repeat":
        this.backgroundRepeatShape(imageContainer, position, size, bounds, bounds.left + position.left + borderData[3], bounds.top + position.top + borderData[0], size.width, size.height, borderData);
        break;
    default:
        this.renderBackgroundRepeat(imageContainer, position, size, {top: bounds.top, left: bounds.left}, borderData[3], borderData[0]);
        break;
    }
};

module.exports = Renderer;

},{"./log":13}],20:[function(_dereq_,module,exports){
var Renderer = _dereq_('../renderer');
var LinearGradientContainer = _dereq_('../lineargradientcontainer');
var log = _dereq_('../log');

function CanvasRenderer(width, height) {
    Renderer.apply(this, arguments);
    this.canvas = this.options.canvas || this.document.createElement("canvas");
    if (!this.options.canvas) {
        this.canvas.width = width;
        this.canvas.height = height;
    }
    this.ctx = this.canvas.getContext("2d");
    this.taintCtx = this.document.createElement("canvas").getContext("2d");
    this.ctx.textBaseline = "bottom";
    this.variables = {};
    log("Initialized CanvasRenderer with size", width, "x", height);
}

CanvasRenderer.prototype = Object.create(Renderer.prototype);

CanvasRenderer.prototype.setFillStyle = function(fillStyle) {
    this.ctx.fillStyle = typeof(fillStyle) === "object" && !!fillStyle.isColor ? fillStyle.toString() : fillStyle;
    return this.ctx;
};

CanvasRenderer.prototype.rectangle = function(left, top, width, height, color) {
    this.setFillStyle(color).fillRect(left, top, width, height);
};

CanvasRenderer.prototype.circle = function(left, top, size, color) {
    this.setFillStyle(color);
    this.ctx.beginPath();
    this.ctx.arc(left + size / 2, top + size / 2, size / 2, 0, Math.PI*2, true);
    this.ctx.closePath();
    this.ctx.fill();
};

CanvasRenderer.prototype.circleStroke = function(left, top, size, color, stroke, strokeColor) {
    this.circle(left, top, size, color);
    this.ctx.strokeStyle = strokeColor.toString();
    this.ctx.stroke();
};

CanvasRenderer.prototype.drawShape = function(shape, color) {
    this.shape(shape);
    this.setFillStyle(color).fill();
};

CanvasRenderer.prototype.taints = function(imageContainer) {
    if (imageContainer.tainted === null) {
        this.taintCtx.drawImage(imageContainer.image, 0, 0);
        try {
            this.taintCtx.getImageData(0, 0, 1, 1);
            imageContainer.tainted = false;
        } catch(e) {
            this.taintCtx = document.createElement("canvas").getContext("2d");
            imageContainer.tainted = true;
        }
    }

    return imageContainer.tainted;
};

CanvasRenderer.prototype.drawImage = function(imageContainer, sx, sy, sw, sh, dx, dy, dw, dh) {
    if (!this.taints(imageContainer) || this.options.allowTaint) {
        this.ctx.drawImage(imageContainer.image, sx, sy, sw, sh, dx, dy, dw, dh);
    }
};

CanvasRenderer.prototype.clip = function(shapes, callback, context) {
    this.ctx.save();
    shapes.filter(hasEntries).forEach(function(shape) {
        this.shape(shape).clip();
    }, this);
    callback.call(context);
    this.ctx.restore();
};

CanvasRenderer.prototype.shape = function(shape) {
    this.ctx.beginPath();
    shape.forEach(function(point, index) {
        if (point[0] === "rect") {
            this.ctx.rect.apply(this.ctx, point.slice(1));
        } else {
            this.ctx[(index === 0) ? "moveTo" : point[0] + "To" ].apply(this.ctx, point.slice(1));
        }
    }, this);
    this.ctx.closePath();
    return this.ctx;
};

CanvasRenderer.prototype.font = function(color, style, variant, weight, size, family) {
    this.setFillStyle(color).font = [style, variant, weight, size, family].join(" ").split(",")[0];
};

CanvasRenderer.prototype.fontShadow = function(color, offsetX, offsetY, blur) {
    this.setVariable("shadowColor", color.toString())
        .setVariable("shadowOffsetY", offsetX)
        .setVariable("shadowOffsetX", offsetY)
        .setVariable("shadowBlur", blur);
};

CanvasRenderer.prototype.clearShadow = function() {
    this.setVariable("shadowColor", "rgba(0,0,0,0)");
};

CanvasRenderer.prototype.setOpacity = function(opacity) {
    this.ctx.globalAlpha = opacity;
};

CanvasRenderer.prototype.setTransform = function(transform) {
    this.ctx.translate(transform.origin[0], transform.origin[1]);
    this.ctx.transform.apply(this.ctx, transform.matrix);
    this.ctx.translate(-transform.origin[0], -transform.origin[1]);
};

CanvasRenderer.prototype.setVariable = function(property, value) {
    if (this.variables[property] !== value) {
        this.variables[property] = this.ctx[property] = value;
    }

    return this;
};

CanvasRenderer.prototype.text = function(text, left, bottom) {
    this.ctx.fillText(text, left, bottom);
};

CanvasRenderer.prototype.backgroundRepeatShape = function(imageContainer, backgroundPosition, size, bounds, left, top, width, height, borderData) {
    var shape = [
        ["line", Math.round(left), Math.round(top)],
        ["line", Math.round(left + width), Math.round(top)],
        ["line", Math.round(left + width), Math.round(height + top)],
        ["line", Math.round(left), Math.round(height + top)]
    ];
    this.clip([shape], function() {
        this.renderBackgroundRepeat(imageContainer, backgroundPosition, size, bounds, borderData[3], borderData[0]);
    }, this);
};

CanvasRenderer.prototype.renderBackgroundRepeat = function(imageContainer, backgroundPosition, size, bounds, borderLeft, borderTop) {
    var offsetX = Math.round(bounds.left + backgroundPosition.left + borderLeft), offsetY = Math.round(bounds.top + backgroundPosition.top + borderTop);
    this.setFillStyle(this.ctx.createPattern(this.resizeImage(imageContainer, size), "repeat"));
    this.ctx.translate(offsetX, offsetY);
    this.ctx.fill();
    this.ctx.translate(-offsetX, -offsetY);
};

CanvasRenderer.prototype.renderBackgroundGradient = function(gradientImage, bounds) {
    if (gradientImage instanceof LinearGradientContainer) {
        var gradient = this.ctx.createLinearGradient(
            bounds.left + bounds.width * gradientImage.x0,
            bounds.top + bounds.height * gradientImage.y0,
            bounds.left +  bounds.width * gradientImage.x1,
            bounds.top +  bounds.height * gradientImage.y1);
        gradientImage.colorStops.forEach(function(colorStop) {
            gradient.addColorStop(colorStop.stop, colorStop.color.toString());
        });
        this.rectangle(bounds.left, bounds.top, bounds.width, bounds.height, gradient);
    }
};

CanvasRenderer.prototype.resizeImage = function(imageContainer, size) {
    var image = imageContainer.image;
    if(image.width === size.width && image.height === size.height) {
        return image;
    }

    var ctx, canvas = document.createElement('canvas');
    canvas.width = size.width;
    canvas.height = size.height;
    ctx = canvas.getContext("2d");
    ctx.drawImage(image, 0, 0, image.width, image.height, 0, 0, size.width, size.height );
    return canvas;
};

function hasEntries(array) {
    return array.length > 0;
}

module.exports = CanvasRenderer;

},{"../lineargradientcontainer":12,"../log":13,"../renderer":19}],21:[function(_dereq_,module,exports){
var NodeContainer = _dereq_('./nodecontainer');

function StackingContext(hasOwnStacking, opacity, element, parent) {
    NodeContainer.call(this, element, parent);
    this.ownStacking = hasOwnStacking;
    this.contexts = [];
    this.children = [];
    this.opacity = (this.parent ? this.parent.stack.opacity : 1) * opacity;
}

StackingContext.prototype = Object.create(NodeContainer.prototype);

StackingContext.prototype.getParentStack = function(context) {
    var parentStack = (this.parent) ? this.parent.stack : null;
    return parentStack ? (parentStack.ownStacking ? parentStack : parentStack.getParentStack(context)) : context.stack;
};

module.exports = StackingContext;

},{"./nodecontainer":14}],22:[function(_dereq_,module,exports){
function Support(document) {
    this.rangeBounds = this.testRangeBounds(document);
    this.cors = this.testCORS();
    this.svg = this.testSVG();
}

Support.prototype.testRangeBounds = function(document) {
    var range, testElement, rangeBounds, rangeHeight, support = false;

    if (document.createRange) {
        range = document.createRange();
        if (range.getBoundingClientRect) {
            testElement = document.createElement('boundtest');
            testElement.style.height = "123px";
            testElement.style.display = "block";
            document.body.appendChild(testElement);

            range.selectNode(testElement);
            rangeBounds = range.getBoundingClientRect();
            rangeHeight = rangeBounds.height;

            if (rangeHeight === 123) {
                support = true;
            }
            document.body.removeChild(testElement);
        }
    }

    return support;
};

Support.prototype.testCORS = function() {
    return typeof((new Image()).crossOrigin) !== "undefined";
};

Support.prototype.testSVG = function() {
    var img = new Image();
    var canvas = document.createElement("canvas");
    var ctx =  canvas.getContext("2d");
    img.src = "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'></svg>";

    try {
        ctx.drawImage(img, 0, 0);
        canvas.toDataURL();
    } catch(e) {
        return false;
    }
    return true;
};

module.exports = Support;

},{}],23:[function(_dereq_,module,exports){
var XHR = _dereq_('./xhr');
var decode64 = _dereq_('./utils').decode64;

function SVGContainer(src) {
    this.src = src;
    this.image = null;
    var self = this;

    this.promise = this.hasFabric().then(function() {
        return (self.isInline(src) ? Promise.resolve(self.inlineFormatting(src)) : XHR(src));
    }).then(function(svg) {
        return new Promise(function(resolve) {
            window.html2canvas.svg.fabric.loadSVGFromString(svg, self.createCanvas.call(self, resolve));
        });
    });
}

SVGContainer.prototype.hasFabric = function() {
    return !window.html2canvas.svg || !window.html2canvas.svg.fabric ? Promise.reject(new Error("html2canvas.svg.js is not loaded, cannot render svg")) : Promise.resolve();
};

SVGContainer.prototype.inlineFormatting = function(src) {
    return (/^data:image\/svg\+xml;base64,/.test(src)) ? this.decode64(this.removeContentType(src)) : this.removeContentType(src);
};

SVGContainer.prototype.removeContentType = function(src) {
    return src.replace(/^data:image\/svg\+xml(;base64)?,/,'');
};

SVGContainer.prototype.isInline = function(src) {
    return (/^data:image\/svg\+xml/i.test(src));
};

SVGContainer.prototype.createCanvas = function(resolve) {
    var self = this;
    return function (objects, options) {
        var canvas = new window.html2canvas.svg.fabric.StaticCanvas('c');
        self.image = canvas.lowerCanvasEl;
        canvas
            .setWidth(options.width)
            .setHeight(options.height)
            .add(window.html2canvas.svg.fabric.util.groupSVGElements(objects, options))
            .renderAll();
        resolve(canvas.lowerCanvasEl);
    };
};

SVGContainer.prototype.decode64 = function(str) {
    return (typeof(window.atob) === "function") ? window.atob(str) : decode64(str);
};

module.exports = SVGContainer;

},{"./utils":26,"./xhr":28}],24:[function(_dereq_,module,exports){
var SVGContainer = _dereq_('./svgcontainer');

function SVGNodeContainer(node, _native) {
    this.src = node;
    this.image = null;
    var self = this;

    this.promise = _native ? new Promise(function(resolve, reject) {
        self.image = new Image();
        self.image.onload = resolve;
        self.image.onerror = reject;
        self.image.src = "data:image/svg+xml," + (new XMLSerializer()).serializeToString(node);
        if (self.image.complete === true) {
            resolve(self.image);
        }
    }) : this.hasFabric().then(function() {
        return new Promise(function(resolve) {
            window.html2canvas.svg.fabric.parseSVGDocument(node, self.createCanvas.call(self, resolve));
        });
    });
}

SVGNodeContainer.prototype = Object.create(SVGContainer.prototype);

module.exports = SVGNodeContainer;

},{"./svgcontainer":23}],25:[function(_dereq_,module,exports){
var NodeContainer = _dereq_('./nodecontainer');

function TextContainer(node, parent) {
    NodeContainer.call(this, node, parent);
}

TextContainer.prototype = Object.create(NodeContainer.prototype);

TextContainer.prototype.applyTextTransform = function() {
    this.node.data = this.transform(this.parent.css("textTransform"));
};

TextContainer.prototype.transform = function(transform) {
    var text = this.node.data;
    switch(transform){
        case "lowercase":
            return text.toLowerCase();
        case "capitalize":
            return text.replace(/(^|\s|:|-|\(|\))([a-z])/g, capitalize);
        case "uppercase":
            return text.toUpperCase();
        default:
            return text;
    }
};

function capitalize(m, p1, p2) {
    if (m.length > 0) {
        return p1 + p2.toUpperCase();
    }
}

module.exports = TextContainer;

},{"./nodecontainer":14}],26:[function(_dereq_,module,exports){
exports.smallImage = function smallImage() {
    return "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
};

exports.bind = function(callback, context) {
    return function() {
        return callback.apply(context, arguments);
    };
};

/*
 * base64-arraybuffer
 * https://github.com/niklasvh/base64-arraybuffer
 *
 * Copyright (c) 2012 Niklas von Hertzen
 * Licensed under the MIT license.
 */

exports.decode64 = function(base64) {
    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var len = base64.length, i, encoded1, encoded2, encoded3, encoded4, byte1, byte2, byte3;

    var output = "";

    for (i = 0; i < len; i+=4) {
        encoded1 = chars.indexOf(base64[i]);
        encoded2 = chars.indexOf(base64[i+1]);
        encoded3 = chars.indexOf(base64[i+2]);
        encoded4 = chars.indexOf(base64[i+3]);

        byte1 = (encoded1 << 2) | (encoded2 >> 4);
        byte2 = ((encoded2 & 15) << 4) | (encoded3 >> 2);
        byte3 = ((encoded3 & 3) << 6) | encoded4;
        if (encoded3 === 64) {
            output += String.fromCharCode(byte1);
        } else if (encoded4 === 64 || encoded4 === -1) {
            output += String.fromCharCode(byte1, byte2);
        } else{
            output += String.fromCharCode(byte1, byte2, byte3);
        }
    }

    return output;
};

exports.getBounds = function(node) {
    if (node.getBoundingClientRect) {
        var clientRect = node.getBoundingClientRect();
        var width = node.offsetWidth == null ? clientRect.width : node.offsetWidth;
        return {
            top: clientRect.top,
            bottom: clientRect.bottom || (clientRect.top + clientRect.height),
            right: clientRect.left + width,
            left: clientRect.left,
            width:  width,
            height: node.offsetHeight == null ? clientRect.height : node.offsetHeight
        };
    }
    return {};
};

exports.offsetBounds = function(node) {
    var parent = node.offsetParent ? exports.offsetBounds(node.offsetParent) : {top: 0, left: 0};

    return {
        top: node.offsetTop + parent.top,
        bottom: node.offsetTop + node.offsetHeight + parent.top,
        right: node.offsetLeft + parent.left + node.offsetWidth,
        left: node.offsetLeft + parent.left,
        width: node.offsetWidth,
        height: node.offsetHeight
    };
};

exports.parseBackgrounds = function(backgroundImage) {
    var whitespace = ' \r\n\t',
        method, definition, prefix, prefix_i, block, results = [],
        mode = 0, numParen = 0, quote, args;
    var appendResult = function() {
        if(method) {
            if (definition.substr(0, 1) === '"') {
                definition = definition.substr(1, definition.length - 2);
            }
            if (definition) {
                args.push(definition);
            }
            if (method.substr(0, 1) === '-' && (prefix_i = method.indexOf('-', 1 ) + 1) > 0) {
                prefix = method.substr(0, prefix_i);
                method = method.substr(prefix_i);
            }
            results.push({
                prefix: prefix,
                method: method.toLowerCase(),
                value: block,
                args: args,
                image: null
            });
        }
        args = [];
        method = prefix = definition = block = '';
    };
    args = [];
    method = prefix = definition = block = '';
    backgroundImage.split("").forEach(function(c) {
        if (mode === 0 && whitespace.indexOf(c) > -1) {
            return;
        }
        switch(c) {
        case '"':
            if(!quote) {
                quote = c;
            } else if(quote === c) {
                quote = null;
            }
            break;
        case '(':
            if(quote) {
                break;
            } else if(mode === 0) {
                mode = 1;
                block += c;
                return;
            } else {
                numParen++;
            }
            break;
        case ')':
            if (quote) {
                break;
            } else if(mode === 1) {
                if(numParen === 0) {
                    mode = 0;
                    block += c;
                    appendResult();
                    return;
                } else {
                    numParen--;
                }
            }
            break;

        case ',':
            if (quote) {
                break;
            } else if(mode === 0) {
                appendResult();
                return;
            } else if (mode === 1) {
                if (numParen === 0 && !method.match(/^url$/i)) {
                    args.push(definition);
                    definition = '';
                    block += c;
                    return;
                }
            }
            break;
        }

        block += c;
        if (mode === 0) {
            method += c;
        } else {
            definition += c;
        }
    });

    appendResult();
    return results;
};

},{}],27:[function(_dereq_,module,exports){
var GradientContainer = _dereq_('./gradientcontainer');

function WebkitGradientContainer(imageData) {
    GradientContainer.apply(this, arguments);
    this.type = imageData.args[0] === "linear" ? GradientContainer.TYPES.LINEAR : GradientContainer.TYPES.RADIAL;
}

WebkitGradientContainer.prototype = Object.create(GradientContainer.prototype);

module.exports = WebkitGradientContainer;

},{"./gradientcontainer":9}],28:[function(_dereq_,module,exports){
function XHR(url) {
    return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);

        xhr.onload = function() {
            if (xhr.status === 200) {
                resolve(xhr.responseText);
            } else {
                reject(new Error(xhr.statusText));
            }
        };

        xhr.onerror = function() {
            reject(new Error("Network Error"));
        };

        xhr.send();
    });
}

module.exports = XHR;

},{}]},{},[4])(4)
});
// export default
'use strict';

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) arr2[i] = arr[i]; return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

var OrgChart = (function () {
  function OrgChart(options) {
    _classCallCheck(this, OrgChart);

    this._name = 'OrgChart';
    Promise.prototype['finally'] = function (callback) {
      var P = this.constructor;

      return this.then(function (value) {
        return P.resolve(callback()).then(function () {
          return value;
        });
      }, function (reason) {
        return P.resolve(callback()).then(function () {
          throw reason;
        });
      });
    };

    var that = this,
        defaultOptions = {
      'nodeTitle': 'name',
      'nodeId': 'id',
      'toggleSiblingsResp': false,
      'depth': 999,
      'chartClass': '',
      'exportButton': false,
      'exportFilename': 'OrgChart',
      'parentNodeSymbol': 'fa-users',
      'draggable': false,
      'direction': 't2b',
      'pan': false,
      'zoom': false
    },
        opts = Object.assign(defaultOptions, options),
        data = opts.data,
        chart = document.createElement('div'),
        chartContainer = document.querySelector(opts.chartContainer);

    this.options = opts;
    delete this.options.data;
    this.chart = chart;
    this.chartContainer = chartContainer;
    chart.dataset.options = JSON.stringify(opts);
    chart.setAttribute('class', 'orgchart' + (opts.chartClass !== '' ? ' ' + opts.chartClass : '') + (opts.direction !== 't2b' ? ' ' + opts.direction : ''));
    if (typeof data === 'object') {
      // local json datasource
      this.buildHierarchy(chart, opts.ajaxURL ? data : this._attachRel(data, '00'), 0);
    } else if (typeof data === 'string' && data.startsWith('#')) {
      // ul datasource
      this.buildHierarchy(chart, this._buildJsonDS(document.querySelector(data).children[0]), 0);
    } else {
      // ajax datasource
      var spinner = document.createElement('i');

      spinner.setAttribute('class', 'fa fa-circle-o-notch fa-spin spinner');
      chart.appendChild(spinner);
      this._getJSON(data).then(function (resp) {
        that.buildHierarchy(chart, opts.ajaxURL ? resp : that._attachRel(resp, '00'), 0);
      })['catch'](function (err) {
        console.error('failed to fetch datasource for orgchart', err);
      })['finally'](function () {
        var spinner = chart.querySelector('.spinner');

        spinner.parentNode.removeChild(spinner);
      });
    }
    chart.addEventListener('click', this._clickChart.bind(this));
    // append the export button to the chart-container
    if (opts.exportButton && !chartContainer.querySelector('.oc-export-btn')) {
      var exportBtn = document.createElement('button'),
          downloadBtn = document.createElement('a');

      exportBtn.setAttribute('class', 'oc-export-btn' + (opts.chartClass !== '' ? ' ' + opts.chartClass : ''));
      exportBtn.innerHTML = 'Export';
      exportBtn.addEventListener('click', this._clickExportButton.bind(this));
      downloadBtn.setAttribute('class', 'oc-download-btn' + (opts.chartClass !== '' ? ' ' + opts.chartClass : ''));
      downloadBtn.setAttribute('download', opts.exportFilename + '.png');
      chartContainer.appendChild(exportBtn);
      chartContainer.appendChild(downloadBtn);
    }

    if (opts.pan) {
      chartContainer.style.overflow = 'hidden';
      chart.addEventListener('mousedown', this._onPanStart.bind(this));
      chart.addEventListener('touchstart', this._onPanStart.bind(this));
      document.body.addEventListener('mouseup', this._onPanEnd.bind(this));
      document.body.addEventListener('touchend', this._onPanEnd.bind(this));
    }

    if (opts.zoom) {
      chartContainer.addEventListener('wheel', this._onWheeling.bind(this));
      chartContainer.addEventListener('touchstart', this._onTouchStart.bind(this));
      document.body.addEventListener('touchmove', this._onTouchMove.bind(this));
      document.body.addEventListener('touchend', this._onTouchEnd.bind(this));
    }

    chartContainer.appendChild(chart);
  }

  _createClass(OrgChart, [{
    key: '_closest',
    value: function _closest(el, fn) {
      return el && (fn(el) && el !== this.chart ? el : this._closest(el.parentNode, fn));
    }
  }, {
    key: '_siblings',
    value: function _siblings(el, expr) {
      return Array.from(el.parentNode.children).filter(function (child) {
        if (child !== el) {
          if (expr) {
            return el.matches(expr);
          }
          return true;
        }
        return false;
      });
    }
  }, {
    key: '_prevAll',
    value: function _prevAll(el, expr) {
      var sibs = [],
          prevSib = el.previousElementSibling;

      while (prevSib) {
        if (!expr || prevSib.matches(expr)) {
          sibs.push(prevSib);
        }
        prevSib = prevSib.previousElementSibling;
      }
      return sibs;
    }
  }, {
    key: '_nextAll',
    value: function _nextAll(el, expr) {
      var sibs = [];
      var nextSib = el.nextElementSibling;

      while (nextSib) {
        if (!expr || nextSib.matches(expr)) {
          sibs.push(nextSib);
        }
        nextSib = nextSib.nextElementSibling;
      }
      return sibs;
    }
  }, {
    key: '_isVisible',
    value: function _isVisible(el) {
      return el.offsetParent !== null;
    }
  }, {
    key: '_addClass',
    value: function _addClass(elements, classNames) {
      elements.forEach(function (el) {
        if (classNames.indexOf(' ') > 0) {
          classNames.split(' ').forEach(function (className) {
            return el.classList.add(className);
          });
        } else {
          el.classList.add(classNames);
        }
      });
    }
  }, {
    key: '_removeClass',
    value: function _removeClass(elements, classNames) {
      elements.forEach(function (el) {
        if (classNames.indexOf(' ') > 0) {
          classNames.split(' ').forEach(function (className) {
            return el.classList.remove(className);
          });
        } else {
          el.classList.remove(classNames);
        }
      });
    }
  }, {
    key: '_css',
    value: function _css(elements, prop, val) {
      elements.forEach(function (el) {
        el.style[prop] = val;
      });
    }
  }, {
    key: '_removeAttr',
    value: function _removeAttr(elements, attr) {
      elements.forEach(function (el) {
        el.removeAttribute(attr);
      });
    }
  }, {
    key: '_one',
    value: function _one(el, type, listener, self) {
      var one = function one(event) {
        try {
          listener.call(self, event);
        } finally {
          el.removeEventListener(type, one);
        }
      };

      el.addEventListener(type, one);
    }
  }, {
    key: '_getDescElements',
    value: function _getDescElements(ancestors, selector) {
      var results = [];

      ancestors.forEach(function (el) {
        return results.push.apply(results, _toConsumableArray(el.querySelectorAll(selector)));
      });
      return results;
    }
  }, {
    key: '_getJSON',
    value: function _getJSON(url) {
      return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();

        function handler() {
          if (this.readyState !== 4) {
            return;
          }
          if (this.status === 200) {
            resolve(JSON.parse(this.response));
          } else {
            reject(new Error(this.statusText));
          }
        }
        xhr.open('GET', url);
        xhr.onreadystatechange = handler;
        xhr.responseType = 'json';
        // xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send();
      });
    }
  }, {
    key: '_buildJsonDS',
    value: function _buildJsonDS(li) {
      var _this = this;

      var subObj = {
        'name': li.firstChild.textContent.trim(),
        'relationship': (li.parentNode.parentNode.nodeName === 'LI' ? '1' : '0') + (li.parentNode.children.length > 1 ? 1 : 0) + (li.children.length ? 1 : 0)
      };

      if (li.id) {
        subObj.id = li.id;
      }
      if (li.querySelector('ul')) {
        Array.from(li.querySelector('ul').children).forEach(function (el) {
          if (!subObj.children) {
            subObj.children = [];
          }
          subObj.children.push(_this._buildJsonDS(el));
        });
      }
      return subObj;
    }
  }, {
    key: '_attachRel',
    value: function _attachRel(data, flags) {
      data.relationship = flags + (data.children && data.children.length > 0 ? 1 : 0);
      if (data.children) {
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = data.children[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var item = _step.value;

            this._attachRel(item, '1' + (data.children.length > 1 ? 1 : 0));
          }
        } catch (err) {
          _didIteratorError = true;
          _iteratorError = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion && _iterator['return']) {
              _iterator['return']();
            }
          } finally {
            if (_didIteratorError) {
              throw _iteratorError;
            }
          }
        }
      }
      return data;
    }
  }, {
    key: '_repaint',
    value: function _repaint(node) {
      if (node) {
        node.style.offsetWidth = node.offsetWidth;
      }
    }

    // whether the cursor is hovering over the node
  }, {
    key: '_isInAction',
    value: function _isInAction(node) {
      return node.querySelector(':scope > .edge').className.indexOf('fa-') > -1;
    }

    // detect the exist/display state of related node
  }, {
    key: '_getNodeState',
    value: function _getNodeState(node, relation) {
      var _this2 = this;

      var criteria = undefined,
          state = { 'exist': false, 'visible': false };

      if (relation === 'parent') {
        criteria = this._closest(node, function (el) {
          return el.classList && el.classList.contains('nodes');
        });
        if (criteria) {
          state.exist = true;
        }
        if (state.exist && this._isVisible(criteria.parentNode.children[0])) {
          state.visible = true;
        }
      } else if (relation === 'children') {
        criteria = this._closest(node, function (el) {
          return el.nodeName === 'TR';
        }).nextElementSibling;
        if (criteria) {
          state.exist = true;
        }
        if (state.exist && this._isVisible(criteria)) {
          state.visible = true;
        }
      } else if (relation === 'siblings') {
        criteria = this._siblings(this._closest(node, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode);
        if (criteria.length) {
          state.exist = true;
        }
        if (state.exist && criteria.some(function (el) {
          return _this2._isVisible(el);
        })) {
          state.visible = true;
        }
      }

      return state;
    }

    // find the related nodes
  }, {
    key: 'getRelatedNodes',
    value: function getRelatedNodes(node, relation) {
      if (relation === 'parent') {
        return this._closest(node, function (el) {
          return el.classList.contains('nodes');
        }).parentNode.children[0].querySelector('.node');
      } else if (relation === 'children') {
        return Array.from(this._closest(node, function (el) {
          return el.nodeName === 'TABLE';
        }).lastChild.children).map(function (el) {
          return el.querySelector('.node');
        });
      } else if (relation === 'siblings') {
        return this._siblings(this._closest(node, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode).map(function (el) {
          return el.querySelector('.node');
        });
      }
      return [];
    }
  }, {
    key: '_switchHorizontalArrow',
    value: function _switchHorizontalArrow(node) {
      var opts = this.options,
          leftEdge = node.querySelector('.leftEdge'),
          rightEdge = node.querySelector('.rightEdge'),
          temp = this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }).parentNode;

      if (opts.toggleSiblingsResp && (typeof opts.ajaxURL === 'undefined' || this._closest(node, function (el) {
        return el.classList.contains('.nodes');
      }).dataset.siblingsLoaded)) {
        var prevSib = temp.previousElementSibling,
            nextSib = temp.nextElementSibling;

        if (prevSib) {
          if (prevSib.classList.contains('hidden')) {
            leftEdge.classList.add('fa-chevron-left');
            leftEdge.classList.remove('fa-chevron-right');
          } else {
            leftEdge.classList.add('fa-chevron-right');
            leftEdge.classList.remove('fa-chevron-left');
          }
        }
        if (nextSib) {
          if (nextSib.classList.contains('hidden')) {
            rightEdge.classList.add('fa-chevron-right');
            rightEdge.classList.remove('fa-chevron-left');
          } else {
            rightEdge.classList.add('fa-chevron-left');
            rightEdge.classList.remove('fa-chevron-right');
          }
        }
      } else {
        var sibs = this._siblings(temp),
            sibsVisible = sibs.length ? !sibs.some(function (el) {
          return el.classList.contains('hidden');
        }) : false;

        leftEdge.classList.toggle('fa-chevron-right', sibsVisible);
        leftEdge.classList.toggle('fa-chevron-left', !sibsVisible);
        rightEdge.classList.toggle('fa-chevron-left', sibsVisible);
        rightEdge.classList.toggle('fa-chevron-right', !sibsVisible);
      }
    }
  }, {
    key: '_hoverNode',
    value: function _hoverNode(event) {
      var node = event.target,
          flag = false,
          topEdge = node.querySelector(':scope > .topEdge'),
          bottomEdge = node.querySelector(':scope > .bottomEdge'),
          leftEdge = node.querySelector(':scope > .leftEdge');

      if (event.type === 'mouseenter') {
        if (topEdge) {
          flag = this._getNodeState(node, 'parent').visible;
          topEdge.classList.toggle('fa-chevron-up', !flag);
          topEdge.classList.toggle('fa-chevron-down', flag);
        }
        if (bottomEdge) {
          flag = this._getNodeState(node, 'children').visible;
          bottomEdge.classList.toggle('fa-chevron-down', !flag);
          bottomEdge.classList.toggle('fa-chevron-up', flag);
        }
        if (leftEdge) {
          this._switchHorizontalArrow(node);
        }
      } else {
        Array.from(node.querySelectorAll(':scope > .edge')).forEach(function (el) {
          el.classList.remove('fa-chevron-up', 'fa-chevron-down', 'fa-chevron-right', 'fa-chevron-left');
        });
      }
    }

    // define node click event handler
  }, {
    key: '_clickNode',
    value: function _clickNode(event) {
      var clickedNode = event.currentTarget,
          focusedNode = this.chart.querySelector('.focused');

      if (focusedNode) {
        focusedNode.classList.remove('focused');
      }
      clickedNode.classList.add('focused');
    }

    // build the parent node of specific node
  }, {
    key: '_buildParentNode',
    value: function _buildParentNode(currentRoot, nodeData, callback) {
      var that = this,
          table = document.createElement('table');

      nodeData.relationship = '001';
      this._createNode(nodeData, 0).then(function (nodeDiv) {
        var chart = that.chart;

        nodeDiv.classList.remove('slide-up');
        nodeDiv.classList.add('slide-down');
        var parentTr = document.createElement('tr'),
            superiorLine = document.createElement('tr'),
            inferiorLine = document.createElement('tr'),
            childrenTr = document.createElement('tr');

        parentTr.setAttribute('class', 'hidden');
        parentTr.innerHTML = '<td colspan="2"></td>';
        table.appendChild(parentTr);
        superiorLine.setAttribute('class', 'lines hidden');
        superiorLine.innerHTML = '<td colspan="2"><div class="downLine"></div></td>';
        table.appendChild(superiorLine);
        inferiorLine.setAttribute('class', 'lines hidden');
        inferiorLine.innerHTML = '<td class="rightLine">&nbsp;</td><td class="leftLine">&nbsp;</td>';
        table.appendChild(inferiorLine);
        childrenTr.setAttribute('class', 'nodes');
        childrenTr.innerHTML = '<td colspan="2"></td>';
        table.appendChild(childrenTr);
        table.querySelector('td').appendChild(nodeDiv);
        chart.insertBefore(table, chart.children[0]);
        table.children[3].children[0].appendChild(chart.lastChild);
        callback();
      })['catch'](function (err) {
        console.error('Failed to create parent node', err);
      });
    }
  }, {
    key: '_switchVerticalArrow',
    value: function _switchVerticalArrow(arrow) {
      arrow.classList.toggle('fa-chevron-up');
      arrow.classList.toggle('fa-chevron-down');
    }

    // show the parent node of the specified node
  }, {
    key: 'showParent',
    value: function showParent(node) {
      // just show only one superior level
      var temp = this._prevAll(this._closest(node, function (el) {
        return el.classList.contains('nodes');
      }));

      this._removeClass(temp, 'hidden');
      // just show only one line
      this._addClass(Array(temp[0].children).slice(1, -1), 'hidden');
      // show parent node with animation
      var parent = temp[2].querySelector('.node');

      this._one(parent, 'transitionend', function () {
        parent.classList.remove('slide');
        if (this._isInAction(node)) {
          this._switchVerticalArrow(node.querySelector(':scope > .topEdge'));
        }
      }, this);
      this._repaint(parent);
      parent.classList.add('slide');
      parent.classList.remove('slide-down');
    }

    // show the sibling nodes of the specified node
  }, {
    key: 'showSiblings',
    value: function showSiblings(node, direction) {
      var _this3 = this;

      // firstly, show the sibling td tags
      var siblings = [],
          temp = this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }).parentNode;

      if (direction) {
        siblings = direction === 'left' ? this._prevAll(temp) : this._nextAll(temp);
      } else {
        siblings = this._siblings(temp);
      }
      this._removeClass(siblings, 'hidden');
      // secondly, show the lines
      var upperLevel = this._prevAll(this._closest(node, function (el) {
        return el.classList.contains('nodes');
      }));

      temp = Array.from(upperLevel[0].querySelectorAll(':scope > .hidden'));
      if (direction) {
        this._removeClass(temp.slice(0, siblings.length * 2), 'hidden');
      } else {
        this._removeClass(temp, 'hidden');
      }
      // thirdly, do some cleaning stuff
      if (!this._getNodeState(node, 'parent').visible) {
        this._removeClass(upperLevel, 'hidden');
        var _parent = upperLevel[2].querySelector('.node');

        this._one(_parent, 'transitionend', function (event) {
          event.target.classList.remove('slide');
        }, this);
        this._repaint(_parent);
        _parent.classList.add('slide');
        _parent.classList.remove('slide-down');
      }
      // lastly, show the sibling nodes with animation
      siblings.forEach(function (sib) {
        Array.from(sib.querySelectorAll('.node')).forEach(function (node) {
          if (_this3._isVisible(node)) {
            node.classList.add('slide');
            node.classList.remove('slide-left', 'slide-right');
          }
        });
      });
      this._one(siblings[0].querySelector('.slide'), 'transitionend', function () {
        var _this4 = this;

        siblings.forEach(function (sib) {
          _this4._removeClass(Array.from(sib.querySelectorAll('.slide')), 'slide');
        });
        if (this._isInAction(node)) {
          this._switchHorizontalArrow(node);
          node.querySelector('.topEdge').classList.remove('fa-chevron-up');
          node.querySelector('.topEdge').classList.add('fa-chevron-down');
        }
      }, this);
    }

    // hide the sibling nodes of the specified node
  }, {
    key: 'hideSiblings',
    value: function hideSiblings(node, direction) {
      var _this5 = this;

      var nodeContainer = this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }).parentNode,
          siblings = this._siblings(nodeContainer);

      siblings.forEach(function (sib) {
        if (sib.querySelector('.spinner')) {
          _this5.chart.dataset.inAjax = false;
        }
      });

      if (!direction || direction && direction === 'left') {
        var preSibs = this._prevAll(nodeContainer);

        preSibs.forEach(function (sib) {
          Array.from(sib.querySelectorAll('.node')).forEach(function (node) {
            if (_this5._isVisible(node)) {
              node.classList.add('slide', 'slide-right');
            }
          });
        });
      }
      if (!direction || direction && direction !== 'left') {
        var nextSibs = this._nextAll(nodeContainer);

        nextSibs.forEach(function (sib) {
          Array.from(sib.querySelectorAll('.node')).forEach(function (node) {
            if (_this5._isVisible(node)) {
              node.classList.add('slide', 'slide-left');
            }
          });
        });
      }

      var animatedNodes = [];

      this._siblings(nodeContainer).forEach(function (sib) {
        Array.prototype.push.apply(animatedNodes, Array.from(sib.querySelectorAll('.slide')));
      });
      var lines = [];

      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = animatedNodes[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          var _node = _step2.value;

          var temp = this._closest(_node, function (el) {
            return el.classList.contains('nodes');
          }).previousElementSibling;

          lines.push(temp);
          lines.push(temp.previousElementSibling);
        }
      } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion2 && _iterator2['return']) {
            _iterator2['return']();
          }
        } finally {
          if (_didIteratorError2) {
            throw _iteratorError2;
          }
        }
      }

      lines = [].concat(_toConsumableArray(new Set(lines)));
      lines.forEach(function (line) {
        line.style.visibility = 'hidden';
      });

      this._one(animatedNodes[0], 'transitionend', function (event) {
        var _this6 = this;

        lines.forEach(function (line) {
          line.removeAttribute('style');
        });
        var sibs = [];

        if (direction) {
          if (direction === 'left') {
            sibs = this._prevAll(nodeContainer, ':not(.hidden)');
          } else {
            sibs = this._nextAll(nodeContainer, ':not(.hidden)');
          }
        } else {
          sibs = this._siblings(nodeContainer);
        }
        var temp = Array.from(this._closest(nodeContainer, function (el) {
          return el.classList.contains('nodes');
        }).previousElementSibling.querySelectorAll(':scope > :not(.hidden)'));

        var someLines = temp.slice(1, direction ? sibs.length * 2 + 1 : -1);

        this._addClass(someLines, 'hidden');
        this._removeClass(animatedNodes, 'slide');
        sibs.forEach(function (sib) {
          Array.from(sib.querySelectorAll('.node')).slice(1).forEach(function (node) {
            if (_this6._isVisible(node)) {
              node.classList.remove('slide-left', 'slide-right');
              node.classList.add('slide-up');
            }
          });
        });
        sibs.forEach(function (sib) {
          _this6._addClass(Array.from(sib.querySelectorAll('.lines')), 'hidden');
          _this6._addClass(Array.from(sib.querySelectorAll('.nodes')), 'hidden');
          _this6._addClass(Array.from(sib.querySelectorAll('.verticalNodes')), 'hidden');
        });
        this._addClass(sibs, 'hidden');

        if (this._isInAction(node)) {
          this._switchHorizontalArrow(node);
        }
      }, this);
    }

    // recursively hide the ancestor node and sibling nodes of the specified node
  }, {
    key: 'hideParent',
    value: function hideParent(node) {
      var temp = Array.from(this._closest(node, function (el) {
        return el.classList.contains('nodes');
      }).parentNode.children).slice(0, 3);

      if (temp[0].querySelector('.spinner')) {
        this.chart.dataset.inAjax = false;
      }
      // hide the sibling nodes
      if (this._getNodeState(node, 'siblings').visible) {
        this.hideSiblings(node);
      }
      // hide the lines
      var lines = temp.slice(1);

      this._css(lines, 'visibility', 'hidden');
      // hide the superior nodes with transition
      var parent = temp[0].querySelector('.node'),
          grandfatherVisible = this._getNodeState(parent, 'parent').visible;

      if (parent && this._isVisible(parent)) {
        parent.classList.add('slide', 'slide-down');
        this._one(parent, 'transitionend', function () {
          parent.classList.remove('slide');
          this._removeAttr(lines, 'style');
          this._addClass(temp, 'hidden');
        }, this);
      }
      // if the current node has the parent node, hide it recursively
      if (parent && grandfatherVisible) {
        this.hideParent(parent);
      }
    }

    // exposed method
  }, {
    key: 'addParent',
    value: function addParent(currentRoot, data) {
      var that = this;

      this._buildParentNode(currentRoot, data, function () {
        if (!currentRoot.querySelector(':scope > .topEdge')) {
          var topEdge = document.createElement('i');

          topEdge.setAttribute('class', 'edge verticalEdge topEdge fa');
          currentRoot.appendChild(topEdge);
        }
        that.showParent(currentRoot);
      });
    }

    // start up loading status for requesting new nodes
  }, {
    key: '_startLoading',
    value: function _startLoading(arrow, node) {
      var opts = this.options,
          chart = this.chart;

      if (typeof chart.dataset.inAjax !== 'undefined' && chart.dataset.inAjax === 'true') {
        return false;
      }

      arrow.classList.add('hidden');
      var spinner = document.createElement('i');

      spinner.setAttribute('class', 'fa fa-circle-o-notch fa-spin spinner');
      node.appendChild(spinner);
      this._addClass(Array.from(node.querySelectorAll(':scope > *:not(.spinner)')), 'hazy');
      chart.dataset.inAjax = true;

      var exportBtn = this.chartContainer.querySelector('.oc-export-btn' + (opts.chartClass !== '' ? '.' + opts.chartClass : ''));

      if (exportBtn) {
        exportBtn.disabled = true;
      }
      return true;
    }

    // terminate loading status for requesting new nodes
  }, {
    key: '_endLoading',
    value: function _endLoading(arrow, node) {
      var opts = this.options;

      arrow.classList.remove('hidden');
      node.querySelector(':scope > .spinner').remove();
      this._removeClass(Array.from(node.querySelectorAll(':scope > .hazy')), 'hazy');
      this.chart.dataset.inAjax = false;
      var exportBtn = this.chartContainer.querySelector('.oc-export-btn' + (opts.chartClass !== '' ? '.' + opts.chartClass : ''));

      if (exportBtn) {
        exportBtn.disabled = false;
      }
    }

    // define click event handler for the top edge
  }, {
    key: '_clickTopEdge',
    value: function _clickTopEdge(event) {
      event.stopPropagation();
      var that = this,
          topEdge = event.target,
          node = topEdge.parentNode,
          parentState = this._getNodeState(node, 'parent'),
          opts = this.options;

      if (parentState.exist) {
        var temp = this._closest(node, function (el) {
          return el.classList.contains('nodes');
        });
        var _parent2 = temp.parentNode.firstChild.querySelector('.node');

        if (_parent2.classList.contains('slide')) {
          return;
        }
        // hide the ancestor nodes and sibling nodes of the specified node
        if (parentState.visible) {
          this.hideParent(node);
          this._one(_parent2, 'transitionend', function () {
            if (this._isInAction(node)) {
              this._switchVerticalArrow(topEdge);
              this._switchHorizontalArrow(node);
            }
          }, this);
        } else {
          // show the ancestors and siblings
          this.showParent(node);
        }
      } else {
        // load the new parent node of the specified node by ajax request
        var nodeId = topEdge.parentNode.id;

        // start up loading status
        if (this._startLoading(topEdge, node)) {
          // load new nodes
          this._getJSON(typeof opts.ajaxURL.parent === 'function' ? opts.ajaxURL.parent(node.dataset.source) : opts.ajaxURL.parent + nodeId).then(function (resp) {
            if (that.chart.dataset.inAjax === 'true') {
              if (Object.keys(resp).length) {
                that.addParent(node, resp);
              }
            }
          })['catch'](function (err) {
            console.error('Failed to get parent node data.', err);
          })['finally'](function () {
            that._endLoading(topEdge, node);
          });
        }
      }
    }

    // recursively hide the descendant nodes of the specified node
  }, {
    key: 'hideChildren',
    value: function hideChildren(node) {
      var that = this,
          temp = this._nextAll(node.parentNode.parentNode),
          lastItem = temp[temp.length - 1],
          lines = [];

      if (lastItem.querySelector('.spinner')) {
        this.chart.dataset.inAjax = false;
      }
      var descendants = Array.from(lastItem.querySelectorAll('.node')).filter(function (el) {
        return that._isVisible(el);
      }),
          isVerticalDesc = lastItem.classList.contains('verticalNodes');

      if (!isVerticalDesc) {
        descendants.forEach(function (desc) {
          Array.prototype.push.apply(lines, that._prevAll(that._closest(desc, function (el) {
            return el.classList.contains('nodes');
          }), '.lines'));
        });
        lines = [].concat(_toConsumableArray(new Set(lines)));
        this._css(lines, 'visibility', 'hidden');
      }
      this._one(descendants[0], 'transitionend', function (event) {
        this._removeClass(descendants, 'slide');
        if (isVerticalDesc) {
          that._addClass(temp, 'hidden');
        } else {
          lines.forEach(function (el) {
            el.removeAttribute('style');
            el.classList.add('hidden');
            el.parentNode.lastChild.classList.add('hidden');
          });
          this._addClass(Array.from(lastItem.querySelectorAll('.verticalNodes')), 'hidden');
        }
        if (this._isInAction(node)) {
          this._switchVerticalArrow(node.querySelector('.bottomEdge'));
        }
      }, this);
      this._addClass(descendants, 'slide slide-up');
    }

    // show the children nodes of the specified node
  }, {
    key: 'showChildren',
    value: function showChildren(node) {
      var _this7 = this;

      var that = this,
          temp = this._nextAll(node.parentNode.parentNode),
          descendants = [];

      this._removeClass(temp, 'hidden');
      if (temp.some(function (el) {
        return el.classList.contains('verticalNodes');
      })) {
        temp.forEach(function (el) {
          Array.prototype.push.apply(descendants, Array.from(el.querySelectorAll('.node')).filter(function (el) {
            return that._isVisible(el);
          }));
        });
      } else {
        Array.from(temp[2].children).forEach(function (el) {
          Array.prototype.push.apply(descendants, Array.from(el.querySelector('tr').querySelectorAll('.node')).filter(function (el) {
            return that._isVisible(el);
          }));
        });
      }
      // the two following statements are used to enforce browser to repaint
      this._repaint(descendants[0]);
      this._one(descendants[0], 'transitionend', function (event) {
        _this7._removeClass(descendants, 'slide');
        if (_this7._isInAction(node)) {
          _this7._switchVerticalArrow(node.querySelector('.bottomEdge'));
        }
      }, this);
      this._addClass(descendants, 'slide');
      this._removeClass(descendants, 'slide-up');
    }

    // build the child nodes of specific node
  }, {
    key: '_buildChildNode',
    value: function _buildChildNode(appendTo, nodeData, callback) {
      var data = nodeData.children || nodeData.siblings;

      appendTo.querySelector('td').setAttribute('colSpan', data.length * 2);
      this.buildHierarchy(appendTo, { 'children': data }, 0, callback);
    }

    // exposed method
  }, {
    key: 'addChildren',
    value: function addChildren(node, data) {
      var that = this,
          opts = this.options,
          count = 0;

      this.chart.dataset.inEdit = 'addChildren';
      this._buildChildNode.call(this, this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }), data, function () {
        if (++count === data.children.length) {
          if (!node.querySelector('.bottomEdge')) {
            var bottomEdge = document.createElement('i');

            bottomEdge.setAttribute('class', 'edge verticalEdge bottomEdge fa');
            node.appendChild(bottomEdge);
          }
          if (!node.querySelector('.symbol')) {
            var symbol = document.createElement('i');

            symbol.setAttribute('class', 'fa ' + opts.parentNodeSymbol + ' symbol');
            node.querySelector(':scope > .title').appendChild(symbol);
          }
          that.showChildren(node);
          that.chart.dataset.inEdit = '';
        }
      });
    }

    // bind click event handler for the bottom edge
  }, {
    key: '_clickBottomEdge',
    value: function _clickBottomEdge(event) {
      var _this8 = this;

      event.stopPropagation();
      var that = this,
          opts = this.options,
          bottomEdge = event.target,
          node = bottomEdge.parentNode,
          childrenState = this._getNodeState(node, 'children');

      if (childrenState.exist) {
        var temp = this._closest(node, function (el) {
          return el.nodeName === 'TR';
        }).parentNode.lastChild;

        if (Array.from(temp.querySelectorAll('.node')).some(function (node) {
          return _this8._isVisible(node) && node.classList.contains('slide');
        })) {
          return;
        }
        // hide the descendant nodes of the specified node
        if (childrenState.visible) {
          this.hideChildren(node);
        } else {
          // show the descendants
          this.showChildren(node);
        }
      } else {
        // load the new children nodes of the specified node by ajax request
        var nodeId = bottomEdge.parentNode.id;

        if (this._startLoading(bottomEdge, node)) {
          this._getJSON(typeof opts.ajaxURL.children === 'function' ? opts.ajaxURL.children(node.dataset.source) : opts.ajaxURL.children + nodeId).then(function (resp) {
            if (that.chart.dataset.inAjax === 'true') {
              if (resp.children.length) {
                that.addChildren(node, resp);
              }
            }
          })['catch'](function (err) {
            console.error('Failed to get children nodes data', err);
          })['finally'](function () {
            that._endLoading(bottomEdge, node);
          });
        }
      }
    }

    // subsequent processing of build sibling nodes
  }, {
    key: '_complementLine',
    value: function _complementLine(oneSibling, siblingCount, existingSibligCount) {
      var temp = oneSibling.parentNode.parentNode.children;

      temp[0].children[0].setAttribute('colspan', siblingCount * 2);
      temp[1].children[0].setAttribute('colspan', siblingCount * 2);
      for (var i = 0; i < existingSibligCount; i++) {
        var rightLine = document.createElement('td'),
            leftLine = document.createElement('td');

        rightLine.setAttribute('class', 'rightLine topLine');
        rightLine.innerHTML = '&nbsp;';
        temp[2].insertBefore(rightLine, temp[2].children[1]);
        leftLine.setAttribute('class', 'leftLine topLine');
        leftLine.innerHTML = '&nbsp;';
        temp[2].insertBefore(leftLine, temp[2].children[1]);
      }
    }

    // build the sibling nodes of specific node
  }, {
    key: '_buildSiblingNode',
    value: function _buildSiblingNode(nodeChart, nodeData, callback) {
      var _this9 = this;

      var that = this,
          newSiblingCount = nodeData.siblings ? nodeData.siblings.length : nodeData.children.length,
          existingSibligCount = nodeChart.parentNode.nodeName === 'TD' ? this._closest(nodeChart, function (el) {
        return el.nodeName === 'TR';
      }).children.length : 1,
          siblingCount = existingSibligCount + newSiblingCount,
          insertPostion = siblingCount > 1 ? Math.floor(siblingCount / 2 - 1) : 0;

      // just build the sibling nodes for the specific node
      if (nodeChart.parentNode.nodeName === 'TD') {
        (function () {
          var temp = _this9._prevAll(nodeChart.parentNode.parentNode);

          temp[0].remove();
          temp[1].remove();
          var childCount = 0;

          that._buildChildNode.call(that, that._closest(nodeChart.parentNode, function (el) {
            return el.nodeName === 'TABLE';
          }), nodeData, function () {
            if (++childCount === newSiblingCount) {
              (function () {
                var siblingTds = Array.from(that._closest(nodeChart.parentNode, function (el) {
                  return el.nodeName === 'TABLE';
                }).lastChild.children);

                if (existingSibligCount > 1) {
                  var _temp = nodeChart.parentNode.parentNode;

                  Array.from(_temp.children).forEach(function (el) {
                    siblingTds[0].parentNode.insertBefore(el, siblingTds[0]);
                  });
                  _temp.remove();
                  that._complementLine(siblingTds[0], siblingCount, existingSibligCount);
                  that._addClass(siblingTds, 'hidden');
                  siblingTds.forEach(function (el) {
                    that._addClass(el.querySelectorAll('.node'), 'slide-left');
                  });
                } else {
                  var _temp2 = nodeChart.parentNode.parentNode;

                  siblingTds[insertPostion].parentNode.insertBefore(nodeChart.parentNode, siblingTds[insertPostion + 1]);
                  _temp2.remove();
                  that._complementLine(siblingTds[insertPostion], siblingCount, 1);
                  that._addClass(siblingTds, 'hidden');
                  that._addClass(that._getDescElements(siblingTds.slice(0, insertPostion + 1), '.node'), 'slide-right');
                  that._addClass(that._getDescElements(siblingTds.slice(insertPostion + 1), '.node'), 'slide-left');
                }
                callback();
              })();
            }
          });
        })();
      } else {
        (function () {
          // build the sibling nodes and parent node for the specific ndoe
          var nodeCount = 0;

          that.buildHierarchy.call(that, that.chart, nodeData, 0, function () {
            if (++nodeCount === siblingCount) {
              var temp = nodeChart.nextElementSibling.children[3].children[insertPostion],
                  td = document.createElement('td');

              td.setAttribute('colspan', 2);
              td.appendChild(nodeChart);
              temp.parentNode.insertBefore(td, temp.nextElementSibling);
              that._complementLine(temp, siblingCount, 1);

              var temp2 = that._closest(nodeChart, function (el) {
                return el.classList && el.classList.contains('nodes');
              }).parentNode.children[0];

              temp2.classList.add('hidden');
              that._addClass(Array.from(temp2.querySelectorAll('.node')), 'slide-down');

              var temp3 = _this9._siblings(nodeChart.parentNode);

              that._addClass(temp3, 'hidden');
              that._addClass(that._getDescElements(temp3.slice(0, insertPostion), '.node'), 'slide-right');
              that._addClass(that._getDescElements(temp3.slice(insertPostion), '.node'), 'slide-left');
              callback();
            }
          });
        })();
      }
    }
  }, {
    key: 'addSiblings',
    value: function addSiblings(node, data) {
      var that = this;

      this.chart.dataset.inEdit = 'addSiblings';
      this._buildSiblingNode.call(this, this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }), data, function () {
        that._closest(node, function (el) {
          return el.classList && el.classList.contains('nodes');
        }).dataset.siblingsLoaded = true;
        if (!node.querySelector('.leftEdge')) {
          var rightEdge = document.createElement('i'),
              leftEdge = document.createElement('i');

          rightEdge.setAttribute('class', 'edge horizontalEdge rightEdge fa');
          node.appendChild(rightEdge);
          leftEdge.setAttribute('class', 'edge horizontalEdge leftEdge fa');
          node.appendChild(leftEdge);
        }
        that.showSiblings(node);
        that.chart.dataset.inEdit = '';
      });
    }
  }, {
    key: 'removeNodes',
    value: function removeNodes(node) {
      var parent = this._closest(node, function (el) {
        return el.nodeName === 'TABLE';
      }).parentNode,
          sibs = this._siblings(parent.parentNode);

      if (parent.nodeName === 'TD') {
        if (this._getNodeState(node, 'siblings').exist) {
          sibs[2].querySelector('.topLine').nextElementSibling.remove();
          sibs[2].querySelector('.topLine').remove();
          sibs[0].children[0].setAttribute('colspan', sibs[2].children.length);
          sibs[1].children[0].setAttribute('colspan', sibs[2].children.length);
          parent.remove();
        } else {
          sibs[0].children[0].removeAttribute('colspan');
          sibs[0].querySelector('.bottomEdge').remove();
          this._siblings(sibs[0]).forEach(function (el) {
            return el.remove();
          });
        }
      } else {
        Array.from(parent.parentNode.children).forEach(function (el) {
          return el.remove();
        });
      }
    }

    // bind click event handler for the left and right edges
  }, {
    key: '_clickHorizontalEdge',
    value: function _clickHorizontalEdge(event) {
      var _this10 = this;

      event.stopPropagation();
      var that = this,
          opts = this.options,
          hEdge = event.target,
          node = hEdge.parentNode,
          siblingsState = this._getNodeState(node, 'siblings');

      if (siblingsState.exist) {
        var temp = this._closest(node, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode,
            siblings = this._siblings(temp);

        if (siblings.some(function (el) {
          var node = el.querySelector('.node');

          return _this10._isVisible(node) && node.classList.contains('slide');
        })) {
          return;
        }
        if (opts.toggleSiblingsResp) {
          var prevSib = this._closest(node, function (el) {
            return el.nodeName === 'TABLE';
          }).parentNode.previousElementSibling,
              nextSib = this._closest(node, function (el) {
            return el.nodeName === 'TABLE';
          }).parentNode.nextElementSibling;

          if (hEdge.classList.contains('leftEdge')) {
            if (prevSib.classList.contains('hidden')) {
              this.showSiblings(node, 'left');
            } else {
              this.hideSiblings(node, 'left');
            }
          } else {
            if (nextSib.classList.contains('hidden')) {
              this.showSiblings(node, 'right');
            } else {
              this.hideSiblings(node, 'right');
            }
          }
        } else {
          if (siblingsState.visible) {
            this.hideSiblings(node);
          } else {
            this.showSiblings(node);
          }
        }
      } else {
        // load the new sibling nodes of the specified node by ajax request
        var nodeId = hEdge.parentNode.id,
            url = this._getNodeState(node, 'parent').exist ? typeof opts.ajaxURL.siblings === 'function' ? opts.ajaxURL.siblings(JSON.parse(node.dataset.source)) : opts.ajaxURL.siblings + nodeId : typeof opts.ajaxURL.families === 'function' ? opts.ajaxURL.families(JSON.parse(node.dataset.source)) : opts.ajaxURL.families + nodeId;

        if (this._startLoading(hEdge, node)) {
          this._getJSON(url).then(function (resp) {
            if (that.chart.dataset.inAjax === 'true') {
              if (resp.siblings || resp.children) {
                that.addSiblings(node, resp);
              }
            }
          })['catch'](function (err) {
            console.error('Failed to get sibling nodes data', err);
          })['finally'](function () {
            that._endLoading(hEdge, node);
          });
        }
      }
    }

    // event handler for toggle buttons in Hybrid(horizontal + vertical) OrgChart
  }, {
    key: '_clickToggleButton',
    value: function _clickToggleButton(event) {
      var that = this,
          toggleBtn = event.target,
          descWrapper = toggleBtn.parentNode.nextElementSibling,
          descendants = Array.from(descWrapper.querySelectorAll('.node')),
          children = Array.from(descWrapper.children).map(function (item) {
        return item.querySelector('.node');
      });

      if (children.some(function (item) {
        return item.classList.contains('slide');
      })) {
        return;
      }
      toggleBtn.classList.toggle('fa-plus-square');
      toggleBtn.classList.toggle('fa-minus-square');
      if (descendants[0].classList.contains('slide-up')) {
        descWrapper.classList.remove('hidden');
        this._repaint(children[0]);
        this._addClass(children, 'slide');
        this._removeClass(children, 'slide-up');
        this._one(children[0], 'transitionend', function () {
          that._removeClass(children, 'slide');
        });
      } else {
        this._addClass(descendants, 'slide slide-up');
        this._one(descendants[0], 'transitionend', function () {
          that._removeClass(descendants, 'slide');
          descendants.forEach(function (desc) {
            var ul = that._closest(desc, function (el) {
              return el.nodeName === 'UL';
            });

            ul.classList.add('hidden');
          });
        });

        descendants.forEach(function (desc) {
          var subTBs = Array.from(desc.querySelectorAll('.toggleBtn'));

          that._removeClass(subTBs, 'fa-minus-square');
          that._addClass(subTBs, 'fa-plus-square');
        });
      }
    }
  }, {
    key: '_dispatchClickEvent',
    value: function _dispatchClickEvent(event) {
      var classList = event.target.classList;

      if (classList.contains('topEdge')) {
        this._clickTopEdge(event);
      } else if (classList.contains('rightEdge') || classList.contains('leftEdge')) {
        this._clickHorizontalEdge(event);
      } else if (classList.contains('bottomEdge')) {
        this._clickBottomEdge(event);
      } else if (classList.contains('toggleBtn')) {
        this._clickToggleButton(event);
      } else {
        this._clickNode(event);
      }
    }
  }, {
    key: '_onDragStart',
    value: function _onDragStart(event) {
      var nodeDiv = event.target,
          opts = this.options,
          isFirefox = /firefox/.test(window.navigator.userAgent.toLowerCase());

      if (isFirefox) {
        event.dataTransfer.setData('text/html', 'hack for firefox');
      }
      // if users enable zoom or direction options
      if (this.chart.style.transform) {
        var ghostNode = undefined,
            nodeCover = undefined;

        if (!document.querySelector('.ghost-node')) {
          ghostNode = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
          ghostNode.classList.add('ghost-node');
          nodeCover = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
          ghostNode.appendChild(nodeCover);
          this.chart.appendChild(ghostNode);
        } else {
          ghostNode = this.chart.querySelector(':scope > .ghost-node');
          nodeCover = ghostNode.children[0];
        }
        var transValues = this.chart.style.transform.split(','),
            scale = Math.abs(window.parseFloat(opts.direction === 't2b' || opts.direction === 'b2t' ? transValues[0].slice(transValues[0].indexOf('(') + 1) : transValues[1]));

        ghostNode.setAttribute('width', nodeDiv.offsetWidth);
        ghostNode.setAttribute('height', nodeDiv.offsetHeight);
        nodeCover.setAttribute('x', 5 * scale);
        nodeCover.setAttribute('y', 5 * scale);
        nodeCover.setAttribute('width', 120 * scale);
        nodeCover.setAttribute('height', 40 * scale);
        nodeCover.setAttribute('rx', 4 * scale);
        nodeCover.setAttribute('ry', 4 * scale);
        nodeCover.setAttribute('stroke-width', 1 * scale);
        var xOffset = event.offsetX * scale,
            yOffset = event.offsetY * scale;

        if (opts.direction === 'l2r') {
          xOffset = event.offsetY * scale;
          yOffset = event.offsetX * scale;
        } else if (opts.direction === 'r2l') {
          xOffset = nodeDiv.offsetWidth - event.offsetY * scale;
          yOffset = event.offsetX * scale;
        } else if (opts.direction === 'b2t') {
          xOffset = nodeDiv.offsetWidth - event.offsetX * scale;
          yOffset = nodeDiv.offsetHeight - event.offsetY * scale;
        }
        if (isFirefox) {
          // hack for old version of Firefox(< 48.0)
          var ghostNodeWrapper = document.createElement('img');

          ghostNodeWrapper.src = 'data:image/svg+xml;utf8,' + new XMLSerializer().serializeToString(ghostNode);
          event.dataTransfer.setDragImage(ghostNodeWrapper, xOffset, yOffset);
          nodeCover.setAttribute('fill', 'rgb(255, 255, 255)');
          nodeCover.setAttribute('stroke', 'rgb(191, 0, 0)');
        } else {
          event.dataTransfer.setDragImage(ghostNode, xOffset, yOffset);
        }
      }
      var dragged = event.target,
          dragZone = this._closest(dragged, function (el) {
        return el.classList && el.classList.contains('nodes');
      }).parentNode.children[0].querySelector('.node'),
          dragHier = Array.from(this._closest(dragged, function (el) {
        return el.nodeName === 'TABLE';
      }).querySelectorAll('.node'));

      this.dragged = dragged;
      Array.from(this.chart.querySelectorAll('.node')).forEach(function (node) {
        if (!dragHier.includes(node)) {
          if (opts.dropCriteria) {
            if (opts.dropCriteria(dragged, dragZone, node)) {
              node.classList.add('allowedDrop');
            }
          } else {
            node.classList.add('allowedDrop');
          }
        }
      });
    }
  }, {
    key: '_onDragOver',
    value: function _onDragOver(event) {
      event.preventDefault();
      var dropZone = event.currentTarget;

      if (!dropZone.classList.contains('allowedDrop')) {
        event.dataTransfer.dropEffect = 'none';
      }
    }
  }, {
    key: '_onDragEnd',
    value: function _onDragEnd(event) {
      Array.from(this.chart.querySelectorAll('.allowedDrop')).forEach(function (el) {
        el.classList.remove('allowedDrop');
      });
    }
  }, {
    key: '_onDrop',
    value: function _onDrop(event) {
      var dropZone = event.currentTarget,
          chart = this.chart,
          dragged = this.dragged,
          dragZone = this._closest(dragged, function (el) {
        return el.classList && el.classList.contains('nodes');
      }).parentNode.children[0].children[0];

      this._removeClass(Array.from(chart.querySelectorAll('.allowedDrop')), 'allowedDrop');
      // firstly, deal with the hierarchy of drop zone
      if (!dropZone.parentNode.parentNode.nextElementSibling) {
        // if the drop zone is a leaf node
        var bottomEdge = document.createElement('i');

        bottomEdge.setAttribute('class', 'edge verticalEdge bottomEdge fa');
        dropZone.appendChild(bottomEdge);
        dropZone.parentNode.setAttribute('colspan', 2);
        var table = this._closest(dropZone, function (el) {
          return el.nodeName === 'TABLE';
        }),
            upperTr = document.createElement('tr'),
            lowerTr = document.createElement('tr'),
            nodeTr = document.createElement('tr');

        upperTr.setAttribute('class', 'lines');
        upperTr.innerHTML = '<td colspan="2"><div class="downLine"></div></td>';
        table.appendChild(upperTr);
        lowerTr.setAttribute('class', 'lines');
        lowerTr.innerHTML = '<td class="rightLine">&nbsp;</td><td class="leftLine">&nbsp;</td>';
        table.appendChild(lowerTr);
        nodeTr.setAttribute('class', 'nodes');
        table.appendChild(nodeTr);
        Array.from(dragged.querySelectorAll('.horizontalEdge')).forEach(function (hEdge) {
          dragged.removeChild(hEdge);
        });
        var draggedTd = this._closest(dragged, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode;

        nodeTr.appendChild(draggedTd);
      } else {
        var dropColspan = window.parseInt(dropZone.parentNode.colSpan) + 2;

        dropZone.parentNode.setAttribute('colspan', dropColspan);
        dropZone.parentNode.parentNode.nextElementSibling.children[0].setAttribute('colspan', dropColspan);
        if (!dragged.querySelector('.horizontalEdge')) {
          var rightEdge = document.createElement('i'),
              leftEdge = document.createElement('i');

          rightEdge.setAttribute('class', 'edge horizontalEdge rightEdge fa');
          dragged.appendChild(rightEdge);
          leftEdge.setAttribute('class', 'edge horizontalEdge leftEdge fa');
          dragged.appendChild(leftEdge);
        }
        var temp = dropZone.parentNode.parentNode.nextElementSibling.nextElementSibling,
            leftline = document.createElement('td'),
            rightline = document.createElement('td');

        leftline.setAttribute('class', 'leftLine topLine');
        leftline.innerHTML = '&nbsp;';
        temp.insertBefore(leftline, temp.children[1]);
        rightline.setAttribute('class', 'rightLine topLine');
        rightline.innerHTML = '&nbsp;';
        temp.insertBefore(rightline, temp.children[2]);
        temp.nextElementSibling.appendChild(this._closest(dragged, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode);

        var dropSibs = this._siblings(this._closest(dragged, function (el) {
          return el.nodeName === 'TABLE';
        }).parentNode).map(function (el) {
          return el.querySelector('.node');
        });

        if (dropSibs.length === 1) {
          var rightEdge = document.createElement('i'),
              leftEdge = document.createElement('i');

          rightEdge.setAttribute('class', 'edge horizontalEdge rightEdge fa');
          dropSibs[0].appendChild(rightEdge);
          leftEdge.setAttribute('class', 'edge horizontalEdge leftEdge fa');
          dropSibs[0].appendChild(leftEdge);
        }
      }
      // secondly, deal with the hierarchy of dragged node
      var dragColSpan = window.parseInt(dragZone.colSpan);

      if (dragColSpan > 2) {
        dragZone.setAttribute('colspan', dragColSpan - 2);
        dragZone.parentNode.nextElementSibling.children[0].setAttribute('colspan', dragColSpan - 2);
        var temp = dragZone.parentNode.nextElementSibling.nextElementSibling;

        temp.children[1].remove();
        temp.children[1].remove();

        var dragSibs = Array.from(dragZone.parentNode.parentNode.children[3].children).map(function (td) {
          return td.querySelector('.node');
        });

        if (dragSibs.length === 1) {
          dragSibs[0].querySelector('.leftEdge').remove();
          dragSibs[0].querySelector('.rightEdge').remove();
        }
      } else {
        dragZone.removeAttribute('colspan');
        dragZone.querySelector('.node').removeChild(dragZone.querySelector('.bottomEdge'));
        Array.from(dragZone.parentNode.parentNode.children).slice(1).forEach(function (tr) {
          return tr.remove();
        });
      }
      var customE = new CustomEvent('nodedropped.orgchart', { 'detail': {
          'draggedNode': dragged,
          'dragZone': dragZone.children[0],
          'dropZone': dropZone
        } });

      chart.dispatchEvent(customE);
    }

    // create node
  }, {
    key: '_createNode',
    value: function _createNode(nodeData, level) {
      var that = this,
          opts = this.options;

      return new Promise(function (resolve, reject) {
        if (nodeData.children) {
          var _iteratorNormalCompletion3 = true;
          var _didIteratorError3 = false;
          var _iteratorError3 = undefined;

          try {
            for (var _iterator3 = nodeData.children[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
              var child = _step3.value;

              child.parentId = nodeData.id;
            }
          } catch (err) {
            _didIteratorError3 = true;
            _iteratorError3 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion3 && _iterator3['return']) {
                _iterator3['return']();
              }
            } finally {
              if (_didIteratorError3) {
                throw _iteratorError3;
              }
            }
          }
        }

        // construct the content of node
        var nodeDiv = document.createElement('div');

        delete nodeData.children;
        nodeDiv.dataset.source = JSON.stringify(nodeData);
        if (nodeData[opts.nodeId]) {
          nodeDiv.id = nodeData[opts.nodeId];
        }
        var inEdit = that.chart.dataset.inEdit,
            isHidden = undefined;

        if (inEdit) {
          isHidden = inEdit === 'addChildren' ? ' slide-up' : '';
        } else {
          isHidden = level >= opts.depth ? ' slide-up' : '';
        }
        nodeDiv.setAttribute('class', 'node ' + (nodeData.className || '') + isHidden);
        if (opts.draggable) {
          nodeDiv.setAttribute('draggable', true);
        }
        if (nodeData.parentId) {
          nodeDiv.setAttribute('data-parent', nodeData.parentId);
        }
        nodeDiv.innerHTML = '\n        <div class="title">' + nodeData[opts.nodeTitle] + '</div>\n        ' + (opts.nodeContent ? '<div class="content">' + nodeData[opts.nodeContent] + '</div>' : '') + '\n      ';
        // append 4 direction arrows or expand/collapse buttons
        var flags = nodeData.relationship || '';

        if (opts.verticalDepth && level + 2 > opts.verticalDepth) {
          if (level + 1 >= opts.verticalDepth && Number(flags.substr(2, 1))) {
            var toggleBtn = document.createElement('i'),
                icon = level + 1 >= opts.depth ? 'plus' : 'minus';

            toggleBtn.setAttribute('class', 'toggleBtn fa fa-' + icon + '-square');
            nodeDiv.appendChild(toggleBtn);
          }
        } else {
          if (Number(flags.substr(0, 1))) {
            var topEdge = document.createElement('i');

            topEdge.setAttribute('class', 'edge verticalEdge topEdge fa');
            nodeDiv.appendChild(topEdge);
          }
          if (Number(flags.substr(1, 1))) {
            var rightEdge = document.createElement('i'),
                leftEdge = document.createElement('i');

            rightEdge.setAttribute('class', 'edge horizontalEdge rightEdge fa');
            nodeDiv.appendChild(rightEdge);
            leftEdge.setAttribute('class', 'edge horizontalEdge leftEdge fa');
            nodeDiv.appendChild(leftEdge);
          }
          if (Number(flags.substr(2, 1))) {
            var bottomEdge = document.createElement('i'),
                symbol = document.createElement('i'),
                title = nodeDiv.querySelector(':scope > .title');

            bottomEdge.setAttribute('class', 'edge verticalEdge bottomEdge fa');
            nodeDiv.appendChild(bottomEdge);
            symbol.setAttribute('class', 'fa ' + opts.parentNodeSymbol + ' symbol');
            title.insertBefore(symbol, title.children[0]);
          }
        }

        nodeDiv.addEventListener('mouseenter', that._hoverNode.bind(that));
        nodeDiv.addEventListener('mouseleave', that._hoverNode.bind(that));
        nodeDiv.addEventListener('click', that._dispatchClickEvent.bind(that));
        if (opts.draggable) {
          nodeDiv.addEventListener('dragstart', that._onDragStart.bind(that));
          nodeDiv.addEventListener('dragover', that._onDragOver.bind(that));
          nodeDiv.addEventListener('dragend', that._onDragEnd.bind(that));
          nodeDiv.addEventListener('drop', that._onDrop.bind(that));
        }
        // allow user to append dom modification after finishing node create of orgchart
        if (opts.createNode) {
          opts.createNode(nodeDiv, nodeData);
        }

        resolve(nodeDiv);
      });
    }
  }, {
    key: 'buildHierarchy',
    value: function buildHierarchy(appendTo, nodeData, level, callback) {
      // Construct the node
      var that = this,
          opts = this.options,
          nodeWrapper = undefined,
          childNodes = nodeData.children,
          isVerticalNode = opts.verticalDepth && level + 1 >= opts.verticalDepth;

      if (Object.keys(nodeData).length > 1) {
        // if nodeData has nested structure
        nodeWrapper = isVerticalNode ? appendTo : document.createElement('table');
        if (!isVerticalNode) {
          appendTo.appendChild(nodeWrapper);
        }
        this._createNode(nodeData, level).then(function (nodeDiv) {
          if (isVerticalNode) {
            nodeWrapper.insertBefore(nodeDiv, nodeWrapper.firstChild);
          } else {
            var tr = document.createElement('tr');

            tr.innerHTML = '\n            <td ' + (childNodes ? 'colspan="' + childNodes.length * 2 + '"' : '') + '>\n            </td>\n          ';
            tr.children[0].appendChild(nodeDiv);
            nodeWrapper.insertBefore(tr, nodeWrapper.children[0] ? nodeWrapper.children[0] : null);
          }
          if (callback) {
            callback();
          }
        })['catch'](function (err) {
          console.error('Failed to creat node', err);
        });
      }
      // Construct the inferior nodes and connectiong lines
      if (childNodes) {
        (function () {
          if (Object.keys(nodeData).length === 1) {
            // if nodeData is just an array
            nodeWrapper = appendTo;
          }
          var isHidden = undefined,
              isVerticalLayer = opts.verticalDepth && level + 2 >= opts.verticalDepth,
              inEdit = that.chart.dataset.inEdit;

          if (inEdit) {
            isHidden = inEdit === 'addSiblings' ? '' : ' hidden';
          } else {
            isHidden = level + 1 >= opts.depth ? ' hidden' : '';
          }

          // draw the line close to parent node
          if (!isVerticalLayer) {
            var tr = document.createElement('tr');

            tr.setAttribute('class', 'lines' + isHidden);
            tr.innerHTML = '\n          <td colspan="' + childNodes.length * 2 + '">\n            <div class="downLine"></div>\n          </td>\n        ';
            nodeWrapper.appendChild(tr);
          }
          // draw the lines close to children nodes
          var lineLayer = document.createElement('tr');

          lineLayer.setAttribute('class', 'lines' + isHidden);
          lineLayer.innerHTML = '\n        <td class="rightLine">&nbsp;</td>\n        ' + childNodes.slice(1).map(function () {
            return '\n          <td class="leftLine topLine">&nbsp;</td>\n          <td class="rightLine topLine">&nbsp;</td>\n          ';
          }).join('') + '\n        <td class="leftLine">&nbsp;</td>\n      ';
          var nodeLayer = undefined;

          if (isVerticalLayer) {
            nodeLayer = document.createElement('ul');
            if (isHidden) {
              nodeLayer.classList.add(isHidden.trim());
            }
            if (level + 2 === opts.verticalDepth) {
              var tr = document.createElement('tr');

              tr.setAttribute('class', 'verticalNodes' + isHidden);
              tr.innerHTML = '<td></td>';
              tr.firstChild.appendChild(nodeLayer);
              nodeWrapper.appendChild(tr);
            } else {
              nodeWrapper.appendChild(nodeLayer);
            }
          } else {
            nodeLayer = document.createElement('tr');
            nodeLayer.setAttribute('class', 'nodes' + isHidden);
            nodeWrapper.appendChild(lineLayer);
            nodeWrapper.appendChild(nodeLayer);
          }
          // recurse through children nodes
          childNodes.forEach(function (child) {
            var nodeCell = undefined;

            if (isVerticalLayer) {
              nodeCell = document.createElement('li');
            } else {
              nodeCell = document.createElement('td');
              nodeCell.setAttribute('colspan', 2);
            }
            nodeLayer.appendChild(nodeCell);
            that.buildHierarchy(nodeCell, child, level + 1, callback);
          });
        })();
      }
    }
  }, {
    key: '_clickChart',
    value: function _clickChart(event) {
      var closestNode = this._closest(event.target, function (el) {
        return el.classList && el.classList.contains('node');
      });

      if (!closestNode && this.chart.querySelector('.node.focused')) {
        this.chart.querySelector('.node.focused').classList.remove('focused');
      }
    }
  }, {
    key: '_clickExportButton',
    value: function _clickExportButton() {
      var opts = this.options,
          chartContainer = this.chartContainer,
          mask = chartContainer.querySelector(':scope > .mask'),
          sourceChart = chartContainer.querySelector('.orgchart:not(.hidden)'),
          flag = opts.direction === 'l2r' || opts.direction === 'r2l';

      if (!mask) {
        mask = document.createElement('div');
        mask.setAttribute('class', 'mask');
        mask.innerHTML = '<i class="fa fa-circle-o-notch fa-spin spinner"></i>';
        chartContainer.appendChild(mask);
      } else {
        mask.classList.remove('hidden');
      }
      chartContainer.classList.add('canvasContainer');
      window.html2canvas(sourceChart, {
        'width': flag ? sourceChart.clientHeight : sourceChart.clientWidth,
        'height': flag ? sourceChart.clientWidth : sourceChart.clientHeight,
        'onclone': function onclone(cloneDoc) {
          var canvasContainer = cloneDoc.querySelector('.canvasContainer');

          canvasContainer.style.overflow = 'visible';
          canvasContainer.querySelector('.orgchart:not(.hidden)').transform = '';
        }
      }).then(function (canvas) {
        var downloadBtn = chartContainer.querySelector('.oc-download-btn');

        // custom modification
        if (!downloadBtn) {
          downloadBtn = document.createElement('a');
          downloadBtn.setAttribute('class', 'oc-download-btn' + (opts.chartClass !== '' ? ' ' + opts.chartClass : ''));
          downloadBtn.setAttribute('download', opts.exportFilename + '.png');
          chartContainer.appendChild(downloadBtn);
        }
        // end custom modification

        chartContainer.querySelector('.mask').classList.add('hidden');
        downloadBtn.setAttribute('href', canvas.toDataURL());
        downloadBtn.click();
      })['catch'](function (err) {
        console.error('Failed to export the curent orgchart!', err);
      })['finally'](function () {
        chartContainer.classList.remove('canvasContainer');
      });
    }
  }, {
    key: '_loopChart',
    value: function _loopChart(chart) {
      var _this11 = this;

      var subObj = { 'id': chart.querySelector('.node').id };

      if (chart.children[3]) {
        Array.from(chart.children[3].children).forEach(function (el) {
          if (!subObj.children) {
            subObj.children = [];
          }
          subObj.children.push(_this11._loopChart(el.firstChild));
        });
      }
      return subObj;
    }
  }, {
    key: 'getHierarchy',
    value: function getHierarchy() {
      if (!this.chart.querySelector('.node').id) {
        return 'Error: Nodes of orghcart to be exported must have id attribute!';
      }
      return this._loopChart(this.chart.querySelector('table'));
    }
  }, {
    key: '_onPanStart',
    value: function _onPanStart(event) {
      var chart = event.currentTarget;

      if (this._closest(event.target, function (el) {
        return el.classList && el.classList.contains('node');
      }) || event.touches && event.touches.length > 1) {
        chart.dataset.panning = false;
        return;
      }
      chart.style.cursor = 'move';
      chart.dataset.panning = true;

      var lastX = 0,
          lastY = 0,
          lastTf = window.getComputedStyle(chart).transform;

      if (lastTf !== 'none') {
        var temp = lastTf.split(',');

        if (!lastTf.includes('3d')) {
          lastX = Number.parseInt(temp[4], 10);
          lastY = Number.parseInt(temp[5], 10);
        } else {
          lastX = Number.parseInt(temp[12], 10);
          lastY = Number.parseInt(temp[13], 10);
        }
      }
      var startX = 0,
          startY = 0;

      if (!event.targetTouches) {
        // pan on desktop
        startX = event.pageX - lastX;
        startY = event.pageY - lastY;
      } else if (event.targetTouches.length === 1) {
        // pan on mobile device
        startX = event.targetTouches[0].pageX - lastX;
        startY = event.targetTouches[0].pageY - lastY;
      } else if (event.targetTouches.length > 1) {
        return;
      }
      chart.dataset.panStart = JSON.stringify({ 'startX': startX, 'startY': startY });
      chart.addEventListener('mousemove', this._onPanning.bind(this));
      chart.addEventListener('touchmove', this._onPanning.bind(this));
    }
  }, {
    key: '_onPanning',
    value: function _onPanning(event) {
      var chart = event.currentTarget;

      if (chart.dataset.panning === 'false') {
        return;
      }
      var newX = 0,
          newY = 0,
          panStart = JSON.parse(chart.dataset.panStart),
          startX = panStart.startX,
          startY = panStart.startY;

      if (!event.targetTouches) {
        // pand on desktop
        newX = event.pageX - startX;
        newY = event.pageY - startY;
      } else if (event.targetTouches.length === 1) {
        // pan on mobile device
        newX = event.targetTouches[0].pageX - startX;
        newY = event.targetTouches[0].pageY - startY;
      } else if (event.targetTouches.length > 1) {
        return;
      }
      var lastTf = window.getComputedStyle(chart).transform;

      if (lastTf === 'none') {
        if (!lastTf.includes('3d')) {
          chart.style.transform = 'matrix(1, 0, 0, 1, ' + newX + ', ' + newY + ')';
        } else {
          chart.style.transform = 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, ' + newX + ', ' + newY + ', 0, 1)';
        }
      } else {
        var matrix = lastTf.split(',');

        if (!lastTf.includes('3d')) {
          matrix[4] = newX;
          matrix[5] = newY + ')';
        } else {
          matrix[12] = newX;
          matrix[13] = newY;
        }
        chart.style.transform = matrix.join(',');
      }
    }
  }, {
    key: '_onPanEnd',
    value: function _onPanEnd(event) {
      var chart = this.chart;

      if (chart.dataset.panning === 'true') {
        chart.dataset.panning = false;
        chart.style.cursor = 'default';
        document.body.removeEventListener('mousemove', this._onPanning);
        document.body.removeEventListener('touchmove', this._onPanning);
      }
    }
  }, {
    key: '_setChartScale',
    value: function _setChartScale(chart, newScale) {
      var lastTf = window.getComputedStyle(chart).transform;

      if (lastTf === 'none') {
        chart.style.transform = 'scale(' + newScale + ',' + newScale + ')';
      } else {
        var matrix = lastTf.split(',');

        if (!lastTf.includes('3d')) {
          matrix[0] = 'matrix(' + newScale;
          matrix[3] = newScale;
          chart.style.transform = lastTf + ' scale(' + newScale + ',' + newScale + ')';
        } else {
          chart.style.transform = lastTf + ' scale3d(' + newScale + ',' + newScale + ', 1)';
        }
      }
      chart.dataset.scale = newScale;
    }
  }, {
    key: '_onWheeling',
    value: function _onWheeling(event) {
      event.preventDefault();

      var newScale = event.deltaY > 0 ? 0.8 : 1.2;

      this._setChartScale(this.chart, newScale);
    }
  }, {
    key: '_getPinchDist',
    value: function _getPinchDist(event) {
      return Math.sqrt((event.touches[0].clientX - event.touches[1].clientX) * (event.touches[0].clientX - event.touches[1].clientX) + (event.touches[0].clientY - event.touches[1].clientY) * (event.touches[0].clientY - event.touches[1].clientY));
    }
  }, {
    key: '_onTouchStart',
    value: function _onTouchStart(event) {
      var chart = this.chart;

      if (event.touches && event.touches.length === 2) {
        var dist = this._getPinchDist(event);

        chart.dataset.pinching = true;
        chart.dataset.pinchDistStart = dist;
      }
    }
  }, {
    key: '_onTouchMove',
    value: function _onTouchMove(event) {
      var chart = this.chart;

      if (chart.dataset.pinching) {
        var dist = this._getPinchDist(event);

        chart.dataset.pinchDistEnd = dist;
      }
    }
  }, {
    key: '_onTouchEnd',
    value: function _onTouchEnd(event) {
      var chart = this.chart;

      if (chart.dataset.pinching) {
        chart.dataset.pinching = false;
        var diff = chart.dataset.pinchDistEnd - chart.dataset.pinchDistStart;

        if (diff > 0) {
          this._setChartScale(chart, 1);
        } else if (diff < 0) {
          this._setChartScale(chart, -1);
        }
      }
    }
  }, {
    key: 'name',
    get: function get() {
      return this._name;
    }
  }]);

  return OrgChart;
})();
//# sourceMappingURL=orgchart.es5.js.map

/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 14.12.16, 22:43
 */

(function($)
{
	$(document).ready(function()
	{
		// init orgchart plugin
		var orgchart = new OrgChart({
			chartContainer: '#dx-orgchart-container',
			data: orgchartData.source,
			nodeContent: 'title',
			depth: orgchartData.displayLevels,
			toggleSiblingsResp: true,
			pan: true,
			// customize node creation process
			createNode: function(node, data)
			{
				var content = $(node).children('.content');
				//content.prepend('<div class="main-icon"><i class="fa fa-sitemap"></i></div>');
				
				if(data.id > 0 && data.count > 0)
					content.append('<div class="pull-left"><a href="' + data.search + '">' + '<i class="fa fa-users"></i> ' + data.count + '</a></div>');
				
				if(data.subordinates > 0)
					content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subdeps') + '">' + data.subordinates + '</div>');
			}
		});
		$("#dx-org-zoom-in").click(function()
		{
			orgchart.set_zoom(-1);
		});
		$("#dx-org-zoom-out").click(function()
		{
			orgchart.set_zoom(1);
		});
		$("#dx-org-export").click(function()
		{
			orgchart._clickExportButton();
		});
	});
})(jQuery);
//# sourceMappingURL=elix_orgdepartments.js.map
