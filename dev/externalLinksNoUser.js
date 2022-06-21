/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

(function () {
    var host = location.host;
    var update = function () {
        var links = document.getElementsByTagName("a");
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            var href = link.getAttribute("href");
            if (href !== null && href.indexOf('//') !== -1 && href.indexOf('//' + host) === -1 && href.indexOf("#") !== 0 && href.indexOf("javascript:") !== 0) {
                if (link.target === null || link.target === '') {
                    link.target = "_blank";
                }
            }
        }
    };
    update();
    window.setInterval(update, 999);
})();