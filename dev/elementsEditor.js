/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.elementsEditor = bearCMS.elementsEditor || (function () {

    var actionsDone = [];

    var addedCSS = [];
    var addCSS = function (code) {
        if (addedCSS.indexOf(code) !== -1) {
            return;
        }
        addedCSS.push(code);
        var style = document.createElement('style');
        style.innerHTML = code;
        document.getElementsByTagName('head')[0].appendChild(style);
    };

    var isColumnsElement = function (element) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf('bearcms-columns-element') !== -1 : false;
    };

    var isFloatingBoxElement = function (element) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf('bearcms-floating-box-element') !== -1 : false;
    };

    var updateColumnsStyle = function (element) {

        var widths = element.getAttribute('data-bearcms-columns-widths');
        if (widths === null || widths.length === 0) {
            widths = ';';
        }

        var columnsWidths = widths.split(';');
        var columnsCount = columnsWidths.length;

        var actionID = 'css-columns-widths-' + widths;
        if (actionsDone.indexOf(actionID) === -1) {

            var columnsStyles = [];

            var notEmptyColumnsWidthsCalc = [];
            var emptyColumnsWidths = 0;
            for (var i = 0; i < columnsCount; i++) {
                if (columnsWidths[i].length === 0) {
                    emptyColumnsWidths++;
                } else {
                    notEmptyColumnsWidthsCalc.push(columnsWidths[i]);
                }
            }
            notEmptyColumnsWidthsCalc = notEmptyColumnsWidthsCalc.join(' + ');

            for (var i = 0; i < columnsCount; i++) {
                var columnWidth = columnsWidths[i];
                var isFixedWidth = columnWidth.indexOf('px') !== -1;
                if (columnWidth.length === 0) {
                    columnWidth = (notEmptyColumnsWidthsCalc.length === 0 ? '100%' : '(100% - (' + notEmptyColumnsWidthsCalc + '))') + '/' + emptyColumnsWidths;
                }
                columnsStyles[i] = isFixedWidth ? 'flex:0 0 auto;width:' + columnWidth + ';' : 'flex:1 0 auto;max-width:calc(' + columnWidth + ' - (var(--bearcms-elements-spacing)*' + (columnsCount - 1) + '/' + columnsCount + '));';
            }

            var selectorPrefix = '.bearcms-columns-element[data-bearcms-columns-widths="' + widths + '"]';
            var notEditableSelector = ':not([data-rvr-editable])';

            var styles = '';
            var emptySelectorPart = '';
            for (var i = 0; i < columnsCount; i++) {
                styles += selectorPrefix + '[data-bearcms-columns-direction="horizontal"]>div:nth-child(' + (i + 1) + '){' + columnsStyles[i] + '}';
                styles += selectorPrefix + '[data-bearcms-columns-direction="vertical"]' + notEditableSelector + '>div:nth-child(' + (i + 1) + '):empty{display:none;}';
                styles += selectorPrefix + '[data-bearcms-columns-direction="vertical-reverse"]' + notEditableSelector + '>div:nth-child(' + (i + 1) + '):empty{display:none;}';
                emptySelectorPart += ':has(> div:nth-child(' + (i + 1) + '):empty)';
            }
            styles += selectorPrefix + notEditableSelector + emptySelectorPart + '{display:none;}';

            addCSS(styles);
            actionsDone.push(actionID);
        }

        var originalColumnKey = 'data-bearcms-columns-element-original-column-index';

        var columnsElements = element.childNodes;

        // add new columns
        while (columnsElements.length < columnsCount) {
            var newColumn = document.createElement('div');
            element.appendChild(newColumn);
        }

        // move previously moved elements to the proper columns
        var elementsToMove = [];
        for (var i = 0; i < columnsCount; i++) {
            var elementsInColumn = columnsElements[i].childNodes;
            for (var j = 0; j < elementsInColumn.length; j++) {
                var elementInColumn = elementsInColumn[j];
                var originalColumnIndex = elementInColumn.getAttribute(originalColumnKey);
                originalColumnIndex = originalColumnIndex !== null ? parseInt(originalColumnIndex, 10) : null;
                if (originalColumnIndex !== null && originalColumnIndex > i) {
                    elementsToMove.push([elementInColumn, originalColumnIndex < columnsCount ? originalColumnIndex : columnsCount - 1]);
                }
            }
        }
        for (var i = 0; i < elementsToMove.length; i++) {
            columnsElements[elementsToMove[i][1]].appendChild(elementsToMove[i][0]);
        }

        // move extra column elements to the last one
        var lastColumn = columnsElements[columnsCount - 1];
        for (var i = columnsCount; i < columnsElements.length; i++) {
            var elementsInColumn = columnsElements[i].childNodes;
            while (elementsInColumn.length > 0) {
                var elementInColumn = elementsInColumn[0];
                if (elementInColumn.getAttribute(originalColumnKey) === null) {
                    elementInColumn.setAttribute(originalColumnKey, i);
                }
                lastColumn.appendChild(elementInColumn);
            }
        }

        // remove extra columns
        while (columnsElements.length > columnsCount) {
            var columnToRemove = columnsElements[columnsCount];
            columnToRemove.parentNode.removeChild(columnToRemove);
        }

    };

    var updateFloatingBoxStyle = function (element) {
        var width = element.getAttribute('data-bearcms-floating-box-width');
        if (width === null || width.length === 0) {
            width = '50%';
        }
        var actionID = 'css-floating-box-width-' + width;
        if (actionsDone.indexOf(actionID) !== -1) {
            return;
        }
        var styles = '';
        var positions = ['left', 'right'];
        for (var i = 0; i < positions.length; i++) {
            var position = positions[i];
            var selector = '.bearcms-floating-box-element[data-bearcms-floating-box-position="' + position + '"][data-bearcms-floating-box-width="' + width + '"]>div:first-child';
            if (width.match(/^[0-9\.]*%$/) !== null && width !== '100%') {
                styles += selector + '{width:calc(' + width + ' - var(--bearcms-elements-spacing)/2);}';
            } else {
                styles += selector + '{width:' + width + ';}';
            }
        }
        addCSS(styles);
        actionsDone.push(actionID);
    };

    var forceUpdateAttributesIfNeeded = function (elements) {
        var update = false;
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (isColumnsElement(element) || isFloatingBoxElement(element)) {
                update = true;
                break;
            }
        }
        if (!update) {
            return;
        }
        try {
            cssToAttributes.run();
        } catch (e) {

        }
        try {
            responsiveAttributes.run();
        } catch (e) {

        }
        try {
            bearCMS.elementEvents.run();
        } catch (e) {

        }
    };

    var styleEditorChange = function (elements) { // called by the CMS
        forceUpdateAttributesIfNeeded(elements);
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (isColumnsElement(element)) {
                updateColumnsStyle(element);
            } else if (isFloatingBoxElement(element)) {
                updateFloatingBoxStyle(element);
            }
        }
    };

    var styleEditorClose = function (elements) { // called by the CMS
        forceUpdateAttributesIfNeeded(elements);
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (isColumnsElement(element)) {
                // Remove original column indexes
                var originalColumnKey = 'data-bearcms-columns-element-original-column-index';
                var elements = element.querySelectorAll('[' + originalColumnKey + ']');
                for (var i = 0; i < elements.length; i++) {
                    elements[i].removeAttribute(originalColumnKey);
                }
            }
        }
    };

    return {
        'styleEditorChange': styleEditorChange,
        'styleEditorClose': styleEditorClose,
    };

}());