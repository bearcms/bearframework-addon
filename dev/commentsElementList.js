/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.commentsElementList = bearCMS.commentsElementList || (function () {

    var loadMore = function (button, data) {
        button.innerHTML += " ...";
        var container = button.parentNode.parentNode;
        var requestData = {
            'serverData': data['serverData'],
            'count': parseInt(container.getAttribute('data-count'), 10) + 10
        };
        clientPackages.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-comments-load-more', requestData).then(function (response) {
                var result = JSON.parse(response);
                clientPackages.get('html5DOMDocument').then(function (html5DOMDocument) {
                    html5DOMDocument.insert(result.html, [container, 'outerHTML']);
                });
            });
        });
    };

    var previewUser = function (provider, id) {
        clientPackages.get('lightbox').then(function (lightbox) {
            lightbox.make();
            clientPackages.get("users").then(function (users) {
                users.openPreview(provider, id);
            });
        });
    };

    return {
        'loadMore': loadMore,
        'previewUser': previewUser
    };

}());