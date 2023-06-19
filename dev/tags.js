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

    var getTargetElement = function (target) { // null|undefined (html), element, #tag (element with tag)
        if (target === null || typeof target === 'undefined') {
            return document.querySelector('html');
        } else if (typeof target === 'string') {
            if (target.substring(0, 1) === '#') {
                return document.querySelector('[' + attributeName + '~="' + target.substring(1) + '"]');
            }
        } else if (typeof element === 'object' && typeof element.tagName !== 'undefined') {
            return element;
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

    return {
        'set': set,
        'exists': exists,
        'remove': remove,
    };

}());