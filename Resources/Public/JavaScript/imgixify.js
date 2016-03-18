(function ($) {
    /**
     * @param options
     * @returns {*}
     */
    $.fn.imgixify = function (options) {
        var defaults = {
            host: null,
            enableHostReplacement: true,
            imgix: {},
            parent: null
        };

        var settings = $.extend({}, defaults, options);

        var chain = $(this).each(function (index, element) {
            var src = $(this).attr('data-src');
            if (!src) {
                log('element does not have a data-src attribute: ' + this);
                return true;
            }

            var url = document.createElement('a');
            url.href = src;
            if (settings.host && settings.enableHostReplacement) {
                url.host = settings.host;
            }

            $(this).attr('data-src', url.toString());
        });

        if (settings.parent && settings.parent.nodeType) {
            imgix.fluid(settings.parent, settings.imgix);
        } else {
            imgix.fluid(settings.imgix);
        }

        observe(settings);

        return chain;
    };

    /**
     * @param settings
     */
    function observe(settings) {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                var newNodes = mutation.addedNodes;
                if (newNodes !== null) {
                    var $nodes = $(newNodes);
                    $nodes.each(function () {
                        var $node = $(this);
                        if ($node.hasClass(settings.imgix.fluidClass)) {
                            $node.imgixify({
                                host: settings.host,
                                enableHostReplacement: settings.enableHostReplacement,
                                imgix: settings.imgix,
                                parent: $node.parent().get(0)
                            });
                        }
                    });
                }
            });
        });
        observer.observe(document.body, {
            attributes: true,
            childList: true,
            subtree: true,
            characterData: true
        });
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