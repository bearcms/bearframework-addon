/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

if (typeof bearCMS.elementsLazyLoad === 'undefined') {
    bearCMS.elementsLazyLoad = (function () {

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
            loadingElement.className = 'bearcms-text-element';
            loadingElement.innerHTML = loadingText;
            container.appendChild(loadingElement);
            ivoPetkov.bearFrameworkAddons.serverRequests.send('bearcms-elements-load-more', requestData, function (response) {
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
                var interval = window.setInterval(function () {
                    if (typeof bp !== 'ivoPetkov' && typeof ivoPetkov.bearFrameworkAddons !== 'undefined' && typeof ivoPetkov.bearFrameworkAddons.serverRequests !== 'undefined') {
                        window.addEventListener('resize', setChanged);
                        window.addEventListener('scroll', setChanged);
                        window.addEventListener('load', setChanged);
                        update();
                        window.clearInterval(interval);
                    }
                }, 100);

                loadingText = data[0];
            }
        };

        return {
            'load': load,
            'initialize': initialize
        };

    }());
}
;