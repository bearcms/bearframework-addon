/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};
bearCMS.blogPostsElement = bearCMS.blogPostsElement || (function () {

    var updateList = function (content, listElement) {
        clientShortcuts.get('-bearcms-html5domdocument').then(function (html5DOMDocument) {
            html5DOMDocument.insert(content, [listElement, 'outerHTML']);
        });
    };

    var loadMore = function (event, data) {
        event.target.innerHTML += " ...";
        var listElement = event.target.parentNode.parentNode.parentNode;
        var requestData = [];
        requestData['serverData'] = data['serverData'];
        clientShortcuts.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-blogposts-load-more', requestData).then(function (response) {
                var result = JSON.parse(response);
                updateList(result.content, listElement);
            });
        });
    };

    return {
        'loadMore': loadMore
    };

}());