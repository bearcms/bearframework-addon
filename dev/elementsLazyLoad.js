/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.elementsLazyLoad = bearCMS.elementsLazyLoad || (function () {

    var loadingText = '';

    var load = function (container, serverData) {
        var requestData = [];
        requestData['serverData'] = serverData;
        try {
            if (typeof bp !== 'undefined' && typeof bp.riverEditor !== 'undefined' && typeof bp.riverEditor.disableInteractions !== 'undefined') {
                bp.riverEditor.disableInteractions();
            }
        } catch (e) {
        }
        var loadingElement = document.createElement('div');
        loadingElement.className = 'bearcms-element';
        loadingElement.innerHTML = '<div class="bearcms-text-element">' + loadingText + '</div>';
        container.appendChild(loadingElement);
        clientPackages.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-elements-load-more', requestData).then(function (response) {
                clientPackages.get('html5DOMDocument').then(function (html5DOMDocument) {
                    loadingElement.parentNode.removeChild(loadingElement);
                    var result = JSON.parse(response);
                    html5DOMDocument.insert(result.content, [container, 'beforeEnd']);
                    if (result.editorContent.length > 0) {
                        html5DOMDocument.insert(result.editorContent);
                    }
                    try {
                        if (typeof bp !== 'undefined' && typeof bp.riverEditor !== 'undefined' && typeof bp.riverEditor.enableInteractions !== 'undefined') {
                            bp.riverEditor.enableInteractions();
                        }
                    } catch (e) {
                    }
                    if (result.nextLazyLoadData.length > 0) {
                        container.setAttribute('data-bearcms-elements-lazy-load', result.nextLazyLoadData);
                        setChanged();
                    }
                });
            });
        });
    };

    var requestAnimationFrameFunction = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || function (callback) {
        window.setTimeout(callback, 1000 / 60);
    };

    var run = function () {
        var elements = document.querySelectorAll('[data-bearcms-elements-lazy-load]');
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (window.innerHeight - element.getBoundingClientRect().bottom > -1000) {
                var serverData = element.getAttribute('data-bearcms-elements-lazy-load');
                element.removeAttribute('data-bearcms-elements-lazy-load');
                load(element, serverData);
            }
        }
    };

    var hasChange = true;
    var update = function () {
        if (hasChange) {
            hasChange = false;
            run();
        }
        requestAnimationFrameFunction.call(null, update);
    };

    var setChanged = function () {
        hasChange = true;
    };

    var initialized = false;

    var initialize = function (data) {
        if (!initialized) {
            initialized = true;
            loadingText = data[0];
            window.addEventListener('resize', setChanged);
            window.addEventListener('scroll', setChanged);
            window.addEventListener('load', setChanged);
            update();
        }
    };

    return {
        'load': load,
        'initialize': initialize
    };

}());