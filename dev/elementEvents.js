/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.elementEvents = bearCMS.elementEvents || (function () {

    if (typeof window.addEventListener !== 'undefined' && typeof document.querySelectorAll !== 'undefined' && typeof IntersectionObserver !== 'undefined') { // Check for old browsers

        var observedElements = [];
        var loadDispatchedElements = [];
        var presentDispatchedElements = [];

        var dispatchEvent = function (element, eventName) {
            var handler = element.getAttribute('data-bearcms-event-' + eventName);
            if (handler !== null && handler !== null) {
                try {
                    var f = new Function(handler);
                    f.apply(element);
                } catch (e) {

                }
            }
        };

        var attributesToObserve = [
            'data-bearcms-event-load',
            'data-bearcms-event-viewport-enter',
            'data-bearcms-event-viewport-leave',
            'data-bearcms-event-present',
        ];
        var attributesToObserveCount = attributesToObserve.length;

        var intersectionObserver = new IntersectionObserver(function (entries) {
            for (var i in entries) {
                var entry = entries[i];
                var element = entry.target;
                if (entry.isIntersecting) {
                    if (presentDispatchedElements.indexOf(element) === -1) {
                        presentDispatchedElements.push(element);
                        dispatchEvent(element, 'present');
                    }
                    dispatchEvent(element, 'viewport-enter');
                } else {
                    if (presentDispatchedElements.indexOf(element) !== -1) {
                        dispatchEvent(element, 'viewport-leave');
                    }
                }
            }
        });

        var run = function () {
            for (var j = 0; j < attributesToObserveCount; j++) {
                var attributeName = attributesToObserve[j];
                var elements = document.querySelectorAll('[' + attributeName + ']');
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    if (observedElements.indexOf(element) === -1) {
                        observedElements.push(element);
                        intersectionObserver.observe(element);
                        if (loadDispatchedElements.indexOf(element) === -1) {
                            loadDispatchedElements.push(element);
                            dispatchEvent(element, 'load');
                        }
                    }
                }
            }
        };

        var initialize = function () {
            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function () {
                    run();
                });
                observer.observe(document.querySelector('body'), { childList: true, subtree: true });
            }
            run();
        };
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize);
        } else {
            initialize();
        }
    } else {
        var run = function () { };
    }

    return {
        'run': run
    };

}());