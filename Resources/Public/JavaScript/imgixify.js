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
        var images = $(this).find("." + settings.imgix.fluidClass);

        if (!images.length) {
            return this;
        }

        images.each(function () {
            var src = $(this).attr("data-src");
            if (!src) {
                log("element does not have a data-src attribute: " + this);
                return true;
            }

            if (!settings.enableFluid) {
                fallback(this, settings);
            } else {
                var url = document.createElement("a");
                url.href = src;
                if (settings.host) {
                    url.host = settings.host;
                }
                $(this).attr("data-src", url.toString());
            }
        });

        if (settings.enableFluid) {
            imgix.fluid($(this).get(0), settings.imgix);
        }

        if (settings.enableObservation) {
            observe(settings);
        }

        return this;
    };

    /**
     * @param image
     * @param settings
     */
    function fallback(image, settings) {
        if ($(image).hasClass(settings.imgix.fluidClass + "-bg")) {
            $(image).css("background-image", 'url("' + $(image).data("src") + '")');
        } else {
            $(image).attr("src", $(image).data("src"));
        }
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
                            $(this).imgixify({
                                host: settings.host,
                                enableFluid: settings.enableFluid,
                                enableObservation: false,
                                imgix: settings.imgix
                            });
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