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
            },
            imgixUrlParams: {
                fit: 'max'
            }
        };

        var settings = $.extend({}, defaults, options);

        if (settings.enableFluid) {
            imgix.fluid($(this).get(0), $.extend({}, settings.imgix, {
                onChangeParamOverride: function (elemWidth, elemHeight, params, elem) {
                    elem.url.urlParts.protocol = window.location.href.protocol || 'https';
                    elem.url.urlParts.host = settings.host;
                    elem.url.setParams(settings.imgixUrlParams);
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
        var nodes = $(node).find("." + settings.imgix.fluidClass);
        $.each(nodes, function (index, node) {
            var _node = $(node);
            if (imgix.isImageElement(node)) {
                _node.attr("src", _node.data("src"));
            } else {
                _node.css("background-image", 'url("' + _node.data("src") + '")');
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