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

        var executeHandler = function (handler, element, event) {
            if (handler !== null && handler !== '') {
                try {
                    var f = new Function('event', handler);
                    f.apply(element, [event]);
                } catch (e) {
                    // ignore errors
                }
            }
        };

        var intersectionObserver = new IntersectionObserver(function (entries) {
            for (var i in entries) {
                var entry = entries[i];
                var element = entry.target;
                updateElementVisiblityData(element, entry.intersectionRatio);
                processAttribute(element, 'data-bearcms-visibility-change', processVisibilityChangeAttribute);
            }
        }, { threshold: [0, 1] });

        var loadDispatchedElements = [];
        var processLoadAttribute = function (element, value) {
            if (loadDispatchedElements.indexOf(element) === -1) {
                loadDispatchedElements.push(element);
                executeHandler(value, element);
            }
        };

        var elementsVisibilityData = []; // the same index as in observedElements
        var updateElementVisiblityData = function (element, intersectionRatio) {
            var index = observedElements.indexOf(element);
            var remove = function (value) {
                var i = elementsVisibilityData[index].indexOf(value);
                if (i !== -1) {
                    elementsVisibilityData[index].splice(i, 1);
                }
            };
            var add = function (value) {
                if (elementsVisibilityData[index].indexOf(value) === -1) {
                    elementsVisibilityData[index].push(value);
                }
            };
            var exists = function (value) {
                return elementsVisibilityData[index].indexOf(value) !== -1;
            };
            if (intersectionRatio === 0) { // not visible
                add('not-visible');
                if (element.getBoundingClientRect().top < 0) {
                    add('is-above-viewport');
                } else {
                    add('is-below-viewport');
                }
                remove('visible');
                remove('fully-visible');
                remove('was-above-viewport');
                remove('was-below-viewport');
            } else {
                if (exists('is-above-viewport')) {
                    add('was-above-viewport');
                }
                if (exists('is-below-viewport')) {
                    add('was-below-viewport');
                }
                remove('not-visible');
                remove('is-above-viewport');
                remove('is-below-viewport');
                if (intersectionRatio === 1) { // fully visible
                    add('fully-visible');
                    add('fully-seen');
                } else {
                    remove('fully-visible');
                }
                add('visible');
                add('seen');
            }
            elementsVisibilityData[index].sort();
        };

        var lastDispatchedVisibilityChangeEvent = []; // key = index + attributeName
        var processVisibilityChangeAttribute = function (element, value, attributeName) {
            if (observedElements.indexOf(element) === -1) {
                observedElements.push(element);
                intersectionObserver.observe(element);
                var index = observedElements.indexOf(element);
                elementsVisibilityData[index] = [];
            } else {
                var index = observedElements.indexOf(element);
                var states = elementsVisibilityData[index];
                var changeEventKey = index + '-' + attributeName;
                if (states.length > 0) {
                    var changeEventValue = states.join(',');
                    if (lastDispatchedVisibilityChangeEvent[changeEventKey] !== 'undefined' && lastDispatchedVisibilityChangeEvent[changeEventKey] === changeEventValue) {
                        return;
                    }
                    lastDispatchedVisibilityChangeEvent[changeEventKey] = changeEventValue;
                    var event = new CustomEvent('visibilityChange', { detail: { states: states } });
                    executeHandler(value, element, event);
                }
            }
        };

        var processAttribute = function (element, attributeName, callback) {
            var attributeValue = element.getAttribute(attributeName);
            if (attributeValue === '*') {
                var elementAttributes = element.attributes;
                for (var j = 0; j < elementAttributes.length; j++) {
                    var elementAttribute = elementAttributes[j];
                    if (elementAttribute.name.indexOf(attributeName + '-') === 0) {
                        callback(element, elementAttribute.value, elementAttribute.name);
                    }
                }
            } else {
                callback(element, attributeValue, attributeName);
            }
        };

        var attributesToObserve = [
            'data-bearcms-load',
            'data-bearcms-visibility-change'
        ];
        var attributesToObserveCount = attributesToObserve.length;
        var run = function () {
            for (var j = 0; j < attributesToObserveCount; j++) {
                var attributeName = attributesToObserve[j];
                var elements = document.querySelectorAll('[' + attributeName + ']');
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    processAttribute(element, 'data-bearcms-load', processLoadAttribute);
                    processAttribute(element, 'data-bearcms-visibility-change', processVisibilityChangeAttribute);
                }
            }
        };

        var initialized = false;
        var initialize = function () {
            if (initialized) {
                return;
            }
            initialized = true;
            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function () {
                    run();
                });
                observer.observe(document.querySelector('body'), { childList: true, subtree: true });
            }
        };
        document.addEventListener('readystatechange', () => { // interactive or complete
            initialize();
            run();
        });
        if (document.readyState === 'complete') {
            initialize();
            run();
        }
    } else {
        var run = function () { };
    }

    var updateVisibilityAttributes = function (element, event, keys) {
        var currentStates = event.detail.states;
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var attributeName = 'data-bearcms-vs-' + key;
            if (currentStates.indexOf(key) === -1) {
                element.removeAttribute(attributeName);
            } else {
                element.setAttribute(attributeName, '');
            }
        }
    };

    var executedOnceLog = [];
    var executeVisibilityCode = function (element, event, data) {
        var index = observedElements.indexOf(element);
        var currentStates = event.detail.states;
        for (var i = 0; i < data.length; i++) {
            var requiredStates = data[i][0];
            var codeToExecute = data[i][1];
            var intersection = requiredStates.filter(function (s) {
                return currentStates.indexOf(s) !== -1;
            });
            if (intersection.length === requiredStates.length) {
                var notSeenValues = requiredStates.filter(function (s) {
                    return s !== 'seen' && s !== 'fully-seen';
                });
                var allSeenValue = notSeenValues.length === 0;
                if (allSeenValue) {
                    var logKey = index + '-' + codeToExecute;
                    if (typeof executedOnceLog[logKey] !== "undefined") {
                        continue;
                    }
                    executedOnceLog[logKey] = 1;
                }
                executeHandler(codeToExecute, element);
            }
        }
    };

    return {
        'run': run,
        'updateVisibilityAttributes': updateVisibilityAttributes,
        'executeVisibilityCode': executeVisibilityCode
    };

}());