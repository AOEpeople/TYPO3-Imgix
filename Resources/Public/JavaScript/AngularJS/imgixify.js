(function (angular) {
    'use strict';

    angular.module('aoe.imgixify', [])
        .value('imgix', window.imgix)
        .run(function (imgix, imgixifySettings, imgixifyService) {
            if (!imgix) {
                return;
            }

            if (imgixifySettings.enableFluid) {
                imgixifyService.initImgixFluid();
            }
        })
        .factory('imgixifySettings', function ($window) {
            var defaults = {
                host: null,
                enableFluid: true,
                enableObservation: false,
                imgix: {
                    fluidClass: 'imgix-fluid'
                },
                imgixUrlParams: {
                    fit: 'max'
                }
            };

            var settings = {};
            if ($window.aoe && $window.aoe.imgix) {
                settings = $window.aoe.imgix.settings || {};
            }
            return angular.extend(defaults, settings);
        })
        .factory('imgixConfig', function (imgixifySettings, $window) {
            return angular.extend(imgixifySettings.imgix, {
                onChangeParamOverride: function (elemWidth, elemHeight, params, elem) {
                    elem.url.urlParts.protocol = $window.location.href.protocol || 'https';
                    elem.url.urlParts.host = imgixifySettings.host;
                    elem.url.setParams(imgixifySettings.imgixUrlParams);
                }
            });
        })
        .factory('imgixifyService', function (imgix, imgixConfig) {

            var imgixFluidSet;
            var isInitialized = false;
            var event = document.createEvent('Event');

            return {
                initImgixFluid: function () {
                    imgixFluidSet = imgix.fluid(imgixConfig);
                    isInitialized = true;
                },

                getImgixFluidSet: function () {
                    return imgixFluidSet;
                },

                isInitialized: function () {
                    return isInitialized;
                },

                refreshImages: function () {
                    event.initEvent('resize', true, true);
                }
            }
        })
        .directive('aoeImgix', function ($window, $timeout, imgixifyService) {
            return {
                restrict: 'A',
                scope: {},
                link: function ($scope, element, attr) {
                    if (!imgixifyService.isInitialized()) {
                        if (isImageElement()) {
                            element.attr('src', element.data('src') || attr.src);
                        } else {
                            element.css("background-image", 'url("' + attr.src + '")');
                        }
                        return;
                    }

                    if (!isElementImgixified()) {
                        $timeout(function () {
                            imgixifyService.getImgixFluidSet().updateSrc(element[0]);
                        });
                    }

                    function isElementImgixified() {
                        return !!element[0].url;
                    }

                    function isImageElement() {
                        return (element[0] && element[0].tagName && element[0].tagName.toLowerCase() === 'img');
                    }
                }
            };
        });

})(angular);