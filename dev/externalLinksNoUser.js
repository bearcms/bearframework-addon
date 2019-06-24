/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var links = document.getElementsByTagName("a");
var host = location.host;
for (var i = 0; i < links.length; i++) {
    var link = links[i];
    var href = link.getAttribute("href");
    if (href !== null && href.indexOf('//') !== -1 && href.indexOf('//' + host) === -1 && href.indexOf("#") !== 0 && href.indexOf("javascript:") !== 0) {
        if (link.target === null || link.target === '') {
            link.target = "_blank";
        }
    }
}