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

    var getElement = function (target) {
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
                            var elementTags = attributeValue.split(' ');
                            if (findTag(targetParts[1], elementTags) !== null) {
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

    var findTag = function (tag, tags) {
        var parts = tag.split('*');
        if (parts.length === 1) {
            return tags.indexOf(tag) !== -1 ? tag : null;
        }
        if (parts[0].length > 0) {
            for (var tag of tags) {
                if (tag.indexOf(parts[0]) === 0) {
                    return tag;
                }
            }
        } else if (parts[1].length > 0) {
            for (var tag of tags) {
                if (tag.substring(tag.length - parts[1].length) === parts[1]) {
                    return tag;
                }
            }
        }
        return null;
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
        var element = getElement(target);
        if (element !== null) {
            var tags = getElementTags(element);
            tags.push(tag);
            setElementTags(element, tags);
            return true;
        }
        return false;
    };

    var exists = function (tag, target) {
        var element = getElement(target);
        if (element !== null) {
            return findTag(tag, getElementTags(element)) !== null;
        }
        return false;
    };

    var get = function (tag, target) {
        var element = getElement(target);
        if (element !== null) {
            return findTag(tag, getElementTags(element));
        }
        return null;
    };

    var remove = function (tag, target) {
        var element = getElement(target);
        if (element !== null) {
            var tags = getElementTags(element);
            var tagToRemove = findTag(tag, tags);
            var updatedTags = tags.filter(function (t) {
                return t !== tagToRemove;
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

    var getList = function (target) {
        var element = getElement(target);
        if (element !== null) {
            return getElementTags(element);
        }
        return [];
    };

    var updateJsFunctionTags = function () { // tag format: js-function-FUNCTIONAME
        if (typeof bearcmsjft === "undefined") {
            return;
        }
        for (var i = 0; i < bearcmsjft.length; i++) {
            var item = bearcmsjft[i];
            var selector = item[0];
            var tag = item[1];
            var functionName = item[2];
            var elements = document.querySelectorAll(selector);
            for (var j = 0; j < elements.length; j++) {
                var element = elements[j];
                try {
                    var func = (new Function('return ' + functionName))();
                } catch (e) {
                    var func = null;
                }
                try {
                    var setTag = func !== null && func(element);
                } catch (e) {
                    var setTag = false;
                }
                if (setTag) {
                    set(tag, element);
                } else {
                    remove(tag, element);
                }

            }
        }
    };

    document.addEventListener('readystatechange', () => { // interactive or complete
        updateJsFunctionTags();
    });
    if (document.readyState === 'complete') {
        updateJsFunctionTags();
    }

    return {
        'set': function (tag, target) {
            var result = set(tag, target);
            updateJsFunctionTags();
            return result;
        },
        'exists': function (tag, target) { // tag can be abc, abc* or *abc
            return exists(tag, target);
        },
        'get': function (tag, target) { // tag can be abc, abc* or *abc
            return get(tag, target);
        },
        'remove': function (tag, target) { // tag can be abc, abc* or *abc
            remove(tag, target);
            updateJsFunctionTags();
        },
        'toggle': function (tag, target) {
            toggle(tag, target);
            updateJsFunctionTags();
        },
        'getList': getList,
        'getElement': getElement,
        'update': function () {
            updateJsFunctionTags();
        }
    };

}());

// var func1 = function (element) {
//     var itemID = bearCMS.tags.get('item-*', [element, 'parent#item-*']);
//     return itemID !== null ? bearCMS.tags.exists('container-' + itemID.replace('item-', ''), [element, 'parent#container']) : false;
// };

// var set1 = function (element, id) {
//     bearCMS.tags.remove('container-*', [element, 'parent#container']);
//     bearCMS.tags.set('container-' + id, [element, 'parent#container']);
// };