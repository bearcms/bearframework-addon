/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.blogPostsElement = bearCMS.blogPostsElement || (function () {

    var loadMore = function (event, data) {
        event.target.innerHTML += " ...";
        var listElement = event.target.parentNode.parentNode.parentNode;
        var requestData = [];
        requestData['serverData'] = data['serverData'];
        clientPackages.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-blogposts-load-more', requestData).then(function (response) {
                var result = JSON.parse(response);
                clientPackages.get('html5DOMDocument').then(function (html5DOMDocument) {
                    html5DOMDocument.insert(result.content, [listElement, 'outerHTML']);
                });
            });
        });
    };

    return {
        'loadMore': loadMore
    };

}());