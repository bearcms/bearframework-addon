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

        var attributeName = 'data-bearcms-element-event';
        var observedElements = [];
        var elementsStates = []; // the same index as in observedElements

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

        var updateElementVisiblityStates = function (element, intersectionRatio) {
            var elementVisibilityData = elementsStates[observedElements.indexOf(element)];
            var remove = function (value) {
                var i = elementVisibilityData.indexOf(value);
                if (i !== -1) {
                    elementVisibilityData.splice(i, 1);
                }
            };
            var add = function (value) {
                if (elementVisibilityData.indexOf(value) === -1) {
                    elementVisibilityData.push(value);
                }
            };
            var exists = function (value) {
                return elementVisibilityData.indexOf(value) !== -1;
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
            elementVisibilityData.sort();
        };

        var callOnceStatas = [];
        var lastsProcessedStates = {};
        var processAttribute = function (element, attributeName, attributeValue) {
            var attributeValueParts = attributeValue.split(' ', 2);
            if (attributeValueParts.length === 2) {
                var statesSelectorParts = attributeValueParts[0].trim().split('+');
                var actionType = attributeValueParts[1].trim(); // attribute or call
                var actionValue = attributeValue.substring(attributeValueParts[0].length + attributeValueParts[1].length + 1).trim();

                if (actionType === 'call') { // prevent calling not-visible code right after load
                    if (statesSelectorParts.indexOf('not-visible') !== -1 && statesSelectorParts.indexOf('seen') === -1) {
                        statesSelectorParts.push('seen');
                    }
                }

                var index = observedElements.indexOf(element);
                var elementStates = elementsStates[index];
                var states = elementStates.filter(function (s) {
                    return statesSelectorParts.indexOf(s) !== -1;
                });
                var attributeKey = index + '-' + attributeName;
                var statesVersion = states.join(',');
                if (typeof lastsProcessedStates[attributeKey] !== 'undefined' && lastsProcessedStates[attributeKey][0] === statesVersion) {
                    return;
                }
                var hasSelectorMatch = function (selectorParts) {
                    for (var i = 0; i < selectorParts.length; i++) {
                        var selectorPart = selectorParts[i];
                        if (states.indexOf(selectorPart) === -1) {
                            return false;
                        }
                    }
                    return true;
                };
                lastsProcessedStates[attributeKey] = [statesVersion, states];
                if (actionType === 'attribute') {
                    if (hasSelectorMatch(statesSelectorParts)) {
                        element.setAttribute(actionValue, '');
                    } else {
                        element.removeAttribute(actionValue);
                    }
                } else if (actionType === 'call') {
                    if (hasSelectorMatch(statesSelectorParts)) {
                        var callMultipleTimesStates = statesSelectorParts.filter(function (s) {
                            return s !== 'load' && s !== 'seen' && s !== 'fully-seen';
                        });
                        if (callMultipleTimesStates.length === 0) {
                            if (callOnceStatas.indexOf(attributeKey) !== -1) {
                                return;
                            }
                            callOnceStatas.push(attributeKey);
                        }
                        var event = new CustomEvent('visibilityChange', { detail: { states: elementStates } });
                        executeHandler(actionValue, element, event);
                    }
                }
            }
        };

        var processAttributes = function (element) {
            var attributeValue = element.getAttribute(attributeName);
            if (attributeValue === '*') {
                var elementAttributes = element.attributes;
                for (var j = 0; j < elementAttributes.length; j++) {
                    var elementAttribute = elementAttributes[j];
                    if (elementAttribute.name.indexOf(attributeName + '-') === 0) {
                        processAttribute(element, elementAttribute.name, elementAttribute.value);
                    }
                }
            } else {
                processAttribute(element, attributeName, attributeValue);
            }
        };

        var intersectionObserver = new IntersectionObserver(function (entries) {
            for (var i in entries) {
                var entry = entries[i];
                var element = entry.target;
                updateElementVisiblityStates(element, entry.intersectionRatio);
                processAttributes(element);
            }
        }, { threshold: [0, 1] });

        var run = function () {
            var elements = document.querySelectorAll('[' + attributeName + ']');
            for (var i = 0; i < elements.length; i++) {
                var element = elements[i];
                if (observedElements.indexOf(element) === -1) {
                    observedElements.push(element);
                    intersectionObserver.observe(element);
                    var index = observedElements.indexOf(element);
                    elementsStates[index] = ['load'];
                    processAttributes(element); // update asap, dont wait for the intersection observer
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

    return {
        'run': run
    };

}());