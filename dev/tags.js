/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.tags = bearCMS.tags || (function () {

    var attributeName = 'data-bearcms-tags';

    var getTargetElement = function (target) {
        if (target === null || typeof target === 'undefined') { // null|undefined (html)
            return document.querySelector('html');
        } else if (typeof target === 'string') {
            if (target.substring(0, 1) === '#') { // #tag (element with tag)
                return document.querySelector('[' + attributeName + '~="' + target.substring(1) + '"]');
            }
        } else if (typeof target === 'object') {
            if (typeof target.tagName !== 'undefined') { // element
                return target;
            } else if (typeof target[0] !== 'undefined' && typeof target[1] !== 'undefined' && typeof target[0].tagName !== 'undefined' && typeof target[1] === 'string') { // [element, parent#tag] (parent with tag), [element, child#tag] (child with tag)
                var element = target[0];
                var targetParts = target[1].split('#');
                if (targetParts[0] === 'parent') {
                    while (element.parentNode) {
                        var attributeValue = element.getAttribute(attributeName);
                        if (attributeValue !== null) {
                            if (attributeValue.split(' ').indexOf(targetParts[1]) !== -1) {
                                return element;
                            }
                        }
                        element = element.parentNode;
                    }
                } else if (targetParts[0] === 'child') {
                    return element.querySelector('[' + attributeName + '~="' + targetParts[1] + '"]');
                }
            }
        }
        return null;
    };

    var getElementTags = function (element) {
        var tags = element.getAttribute(attributeName);
        return tags !== null ? tags.split(' ') : [];
    };

    var setElementTags = function (element, tags) {
        tags = tags.filter(function (t, index, a) {
            return index === a.indexOf(t);
        });
        if (tags.length === 0) {
            element.removeAttribute(attributeName);
        } else {
            element.setAttribute(attributeName, tags.join(' '));
        }
    };

    var set = function (tag, target) {
        var element = getTargetElement(target);
        if (element !== null) {
            var tags = getElementTags(element);
            tags.push(tag);
            setElementTags(element, tags);
            return true;
        }
        return false;
    };

    var exists = function (tag, target) {
        var element = getTargetElement(target);
        if (element !== null) {
            return getElementTags(element).indexOf(tag) !== -1;
        }
        return false;
    };

    var remove = function (tag, target) {
        var element = getTargetElement(target);
        if (element !== null) {
            var tags = getElementTags(element);
            var updatedTags = tags.filter(function (t) {
                return t !== tag;
            });
            setElementTags(element, updatedTags);
        }
    };

    var toggle = function (tag, target) {
        if (exists(tag, target)) {
            remove(tag, target);
        } else {
            set(tag, target);
        }
    };

    return {
        'set': set,
        'exists': exists,
        'remove': remove,
        'toggle': toggle,
    };

}());