(function ($) {
    /**
     * @param options
     * @returns {*}
     */
    $.fn.imgixify = function (options) {
        var defaults = {
            host: null,
            enableFluid: true,
            enableObservation: false,
            imgix: {
                fluidClass: "imgix-fluid"
            }
        };

        var settings = $.extend({}, defaults, options);

        if (settings.enableFluid) {
            imgix.fluid($(this).get(0), $.extend({}, settings.imgix, {
                onChangeParamOverride: function (elemWidth, elemHeight, params, elem) {
                    elem.url.urlParts.protocol = window.location.href.protocol || 'https';
                    elem.url.urlParts.host = settings.host;
                }
            }));
        } else {
            fallback(this, settings);
        }

        if (settings.enableObservation) {
            observe(settings);
        }

        return this;
    };

    /**
     * @param node
     * @param settings
     */
    function fallback(node, settings) {
        $(node).find("." + settings.imgix.fluidClass).each(function () {
            var _this = $(this);
            if (_this.hasClass(settings.imgix.fluidClass + "-bg")) {
                _this.css("background-image", 'url("' + _this.data("src") + '")');
            } else {
                _this.attr("src", _this.data("src"));
            }
        });
    }

    /**
     * @param settings
     */
    function observe(settings) {
        var MutationObserver = (function () {
            var prefixes = ["WebKit", "Moz", "O", "Ms", ""];
            for (var i = 0; i < prefixes.length; i++) {
                if (prefixes[i] + "MutationObserver" in window) {
                    return window[prefixes[i] + "MutationObserver"];
                }
            }
            return false;
        }());
        if (MutationObserver) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    var newNodes = mutation.addedNodes;
                    if (newNodes !== null) {
                        var $nodes = $(newNodes);
                        $nodes.each(function () {
                            if (this.tagName) {
                                $(this).imgixify({
                                    host: settings.host,
                                    enableFluid: settings.enableFluid,
                                    enableObservation: false,
                                    imgix: settings.imgix
                                });
                            }
                        });
                    }
                });
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
        else {
            log("browser do not support mutation observers");
        }
    }

    /**
     * @param msg
     */
    function log(msg) {
        if (window.console && window.console.log) {
            window.console.log(msg);
        }
    }
}(jQuery));