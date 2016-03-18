(function ($) {
    /**
     * @param options
     * @returns {*}
     */
    $.fn.imgixify = function (options) {
        var defaults = {
            host: null,
            enableHostReplacement: true,
            imgix: {}
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

        imgix.fluid(settings.imgix);

        return chain;
    };

    /**
     * @param msg
     */
    function log(msg) {
        if (window.console && window.console.log) {
            window.console.log(msg);
        }
    }
}(jQuery));