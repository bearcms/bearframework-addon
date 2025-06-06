/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.modalContent = bearCMS.modalContent || (function () {

    var lastLightboxContext = null;
    var contentCache = [];

    var open = function (id, options) {
        var options = typeof options !== 'undefined' ? options : {};
        var spacing = typeof options.spacing !== 'undefined' ? options.spacing : '0px';
        var closeOnEscKey = typeof options.closeOnEscKey !== 'undefined' ? options.closeOnEscKey : true;
        var showCloseButton = typeof options.showCloseButton !== 'undefined' ? options.showCloseButton : true;
        var onOpen = typeof options.onOpen !== 'undefined' ? options.onOpen : null;
        var cache = typeof options.cache !== 'undefined' ? options.cache : false;
        clientPackages.get('lightbox').then(function (lightbox) {
            var context = lightbox.make({ closeOnEscKey: closeOnEscKey, showCloseButton: showCloseButton });
            lastLightboxContext = context;
            var open = function (responseText) {
                context.open(responseText, {
                    closeOnEscKey: closeOnEscKey,
                    showCloseButton: showCloseButton,
                    spacing: spacing,
                    onOpen: onOpen
                });
            };
            if (cache && typeof contentCache[id] !== 'undefined') {
                open(contentCache[id]);
                return;
            }
            clientPackages.get('serverRequests').then(function (serverRequests) {
                serverRequests.send('-bearcms-modal-content', { id: id }).then(function (responseText) {
                    open(responseText);
                    if (cache) {
                        contentCache[id] = responseText;
                    }
                });
            });
        });
    };

    var close = function () {
        if (lastLightboxContext !== null) {
            lastLightboxContext.close();
        }
    };

    return {
        'open': open,
        'close': close
    };

}());