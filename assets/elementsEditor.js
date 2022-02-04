/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.elementsEditor = bearCMS.elementsEditor || (function () {

    var addCSS = function (code) {
        var style = document.createElement('style');
        style.innerHTML = code;
        document.getElementsByTagName('head')[0].appendChild(style);
    };

    var removeClassByPrefix = function (element, prefix) {
        var classes = element.classList;
        var classesToRemove = [];
        for (var i = 0; i < classes.length; i++) {
            if (classes[i].indexOf(prefix) === 0) {
                classesToRemove.push(classes[i]);
            }
        }
        for (var i = 0; i < classesToRemove.length; i++) {
            classes.remove(classesToRemove[i]);
        }
    };

    var isHorizontalFlexibleBoxElement = function (element) {
        return element.getAttribute('data-flexible-box-direction') === 'row' && element.getAttribute('data-flexible-box-auto-vertical') !== '1';
    };

    var isColumnsElement = function (element) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf('bearcms-elements-columns') !== -1 : false;
    };

    var isFloatingBoxElement = function (element) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf('bearcms-elements-floating-box') !== -1 : false;
    };

    var isFlexibleBoxElement = function (element) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf('bearcms-elements-flexible-box') !== -1 : false;
    };

    var updateColumnsStyle = function (element, widths) {

        var widths = element.getAttribute('data-columns-elements-editor-widths');
        if (widths === null || widths.length === 0) {
            widths = ',';
        }

        var columnsWidths = widths.split(',');
        var columnsCount = columnsWidths.length;

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
            columnsStyles[i] = 'flex:1 0 auto;min-width:15px;max-width:' + (isFixedWidth ? columnWidth : 'calc(' + columnWidth + ' - (var(--bearcms-elements-spacing)*' + (columnsCount - 1) + '/' + columnsCount + '))') + ';margin-right:' + (columnsCount > i + 1 ? 'var(--bearcms-elements-spacing)' : '0') + ';';
        }

        var className = 'bre' + (new Date()).getTime();

        var styles = '';
        styles += '.' + className + '{display:flex !important;flex-direction:row;}';
        styles += '.' + className + '>div>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
        for (var i = 0; i < columnsCount; i++) {
            styles += '.' + className + '>div:nth-child(' + (i + 1) + '){' + columnsStyles[i] + '}';
        }
        styles += '.' + className + '[data-columns-auto-vertical="1"]{flex-direction:column;}';
        for (var i = 0; i < columnsCount; i++) {
            styles += '.' + className + '[data-columns-auto-vertical="1"]>div:nth-child(' + (i + 1) + '){width:100%;max-width:100%;margin-right:0;}';
        }
        styles += '.' + className + '[data-columns-auto-vertical="1"]>div:not(:empty):not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';
        styles += '.' + className + '[data-rvr-editable][data-columns-auto-vertical="1"]>div:not(:last-child){margin-bottom:var(--bearcms-elements-spacing);}';

        addCSS(styles);

        removeClassByPrefix(element, 'bre');
        element.classList.add(className);

        var originalColumnKey = 'data-original-column-index';

        var columnsElements = element.childNodes;

        // add new columns
        while (columnsElements.length < columnsCount) {
            var newColumn = document.createElement('div');
            newColumn.setAttribute('class', 'bearcms-elements-columns-column');
            newColumn.style.setProperty('--bearcms-elements-spacing', getComputedStyle(element.firstChild).getPropertyValue('--bearcms-elements-spacing'));
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

    var setStructuralElementAutoVerticalWidth = function (element, value, attributeName) {
        var currentValue = element.getAttribute('data-responsive-attributes');
        if (currentValue === null) {
            currentValue = '';
        }
        var newValue = currentValue;
        var match = currentValue.match("w(.*?)=>" + attributeName + "=1");
        if (match !== null) {
            newValue = currentValue.replace(match[0], '');
            if (newValue.length > 0 && newValue[0] === ',') {
                newValue = newValue.substring(1, newValue.length);
            }
        }
        if (value.indexOf('px') !== -1) {
            var valueInPx = parseInt(value.replace('px', ''), 10);
            newValue = 'w<=' + valueInPx + '=>' + attributeName + '=1' + (newValue.length > 0 ? ',' + newValue : '');
        } else {
            element.removeAttribute(attributeName);
        }
        if (newValue !== '') {
            element.setAttribute('data-responsive-attributes', newValue);
        } else {
            element.removeAttribute('data-responsive-attributes');
        }
        if (typeof clientPackages !== 'undefined') {
            clientPackages.get('responsiveAttributes')
                .then(function (responsiveAttributes) {
                    responsiveAttributes.run();
                })
        }
    };

    var setStructuralElementElementsSpacing = function (element, value) {
        var children = element.childNodes;
        for (var i = 0; i < children.length; i++) {
            children[i].style.setProperty("--bearcms-elements-spacing", value === null || value.length === 0 ? 'inherit' : value);
        }
    };

    var setColumnsWidths = function (element, value) {
        element.setAttribute('data-columns-elements-editor-widths', value);
        updateColumnsStyle(element);
    };

    var setColumnsAutoVerticalWidth = function (element, value) {
        setStructuralElementAutoVerticalWidth(element, value, 'data-columns-auto-vertical');
    };

    var setFloatingBoxAutoVerticalWidth = function (element, value) {
        setStructuralElementAutoVerticalWidth(element, value, 'data-floating-box-auto-vertical');
    };

    var setFlexibleBoxAutoVerticalWidth = function (element, value) {
        setStructuralElementAutoVerticalWidth(element, value, 'data-flexible-box-auto-vertical');
    };

    var setFlexibleBoxDirection = function (element, value) {
        element.setAttribute('data-flexible-box-direction', value);
    };

    var setFloatingBoxPosition = function (element, value) {
        element.setAttribute('data-floating-box-position', value);
    };

    var setFloatingBoxWidth = function (element, value) {
        if (value === null || value.length === 0) {
            value = '50%';
        }
        element.style.setProperty("--bearcms-floating-box-width", value.substr(value.length - 1) === '%' && value !== '100%' ? 'calc(' + value + ' - var(--bearcms-elements-spacing)/2)' : value);
    };

    var setColumnsElementsSpacing = function (element, value) {
        setStructuralElementElementsSpacing(element, value);
    };

    var setFloatingBoxElementsSpacing = function (element, value) {
        setStructuralElementElementsSpacing(element, value);
    };

    var setFlexibleBoxElementsSpacing = function (element, value) {
        setStructuralElementElementsSpacing(element, value);
    };

    var setFlexibleBoxRowAlignment = function (element, value) {
        element.setAttribute('data-flexible-box-row-alignment', value);
    };

    var onExitEditor = function () {
        // clean up original column indexes
        var originalColumnKey = 'data-original-column-index';
        var elements = document.querySelectorAll('[' + originalColumnKey + ']');
        for (var i = 0; i < elements.length; i++) {
            elements[i].removeAttribute(originalColumnKey);
        }
    };

    return {
        'isHorizontalFlexibleBoxElement': isHorizontalFlexibleBoxElement,
        'isColumnsElement': isColumnsElement,
        'isFloatingBoxElement': isFloatingBoxElement,
        'isFlexibleBoxElement': isFlexibleBoxElement,
        'setColumnsWidths': setColumnsWidths,
        'setColumnsAutoVerticalWidth': setColumnsAutoVerticalWidth,
        'setColumnsElementsSpacing': setColumnsElementsSpacing,
        'setFloatingBoxPosition': setFloatingBoxPosition,
        'setFloatingBoxWidth': setFloatingBoxWidth,
        'setFloatingBoxAutoVerticalWidth': setFloatingBoxAutoVerticalWidth,
        'setFloatingBoxElementsSpacing': setFloatingBoxElementsSpacing,
        'setFlexibleBoxDirection': setFlexibleBoxDirection,
        'setFlexibleBoxAutoVerticalWidth': setFlexibleBoxAutoVerticalWidth,
        'setFlexibleBoxElementsSpacing': setFlexibleBoxElementsSpacing,
        'setFlexibleBoxRowAlignment': setFlexibleBoxRowAlignment,
        'onExitEditor': onExitEditor
    };

}());