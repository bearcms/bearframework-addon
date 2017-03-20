/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.forumPostsElement = (function () {

    var updateList = function (content, listElement) {
        html5DOMDocument.insert(content, [listElement, 'outerHTML']);
    }

    var loadMore = function (event, data) {
        var listElement = event.target.parentNode.parentNode;
        var requestData = [];
        requestData['serverData'] = data['serverData'];
        ivoPetkov.bearFrameworkAddons.serverRequests.send('bearcms-forumposts-load-more', requestData, function (response) {
            var result = JSON.parse(response);
            updateList(result.content, listElement);
        });
    };

    return {
        'loadMore': loadMore
    };

}());