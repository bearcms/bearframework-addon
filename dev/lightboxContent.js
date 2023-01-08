/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.lightboxContent = bearCMS.lightboxContent || (function () {

    var open = function (id) {
        clientPackages.get('lightbox').then(function (lightbox) {
            var context = lightbox.make();
            clientPackages.get('serverRequests').then(function (serverRequests) {
                serverRequests.send('-bearcms-lightbox-content', { id: id }).then(function (responseText) {
                    console.log(responseText);
                    context.open(responseText);
                });
            });
        });
    };

    return {
        'open': open
    };

}());