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

    var hasClass = function (element, className) {
        var value = element.getAttribute('class');
        return value !== null ? value.indexOf(className) !== -1 : false;
    };

    var isColumnsElement = function (element) {
        return hasClass(element, 'bearcms-columns-element');
    };

    var isFloatingBoxElement = function (element) {
        return hasClass(element, 'bearcms-floating-box-element');
    };

    var isSliderElement = function (element) {
        return hasClass(element, 'bearcms-slider-element');
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

    var forceUpdateElements = function (elements) {
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

    var contentChange = function (elements) {
        forceUpdateElements(elements);
        if (typeof bearCMS.sliderElements !== 'undefined') {
            bearCMS.sliderElements.update();
        }
    };

    var styleEditorChange = function (elements) { // called by the CMS
        forceUpdateElements(elements);
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (isColumnsElement(element)) {
                updateColumnsStyle(element);
            } else if (isFloatingBoxElement(element)) {
                updateFloatingBoxStyle(element);
            } else if (isSliderElement(element)) {
                if (typeof bearCMS.sliderElements !== 'undefined') {
                    bearCMS.sliderElements.update(element);
                }
            }
        }
    };

    var styleEditorClose = function (elements) { // called by the CMS
        forceUpdateElements(elements);
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

    var getElementDefaultStyleOptionsValues = function (element) {
        if (element === null) {
            return null;
        }
        var firstChild = element.firstChild;
        if (firstChild === null) {
            return null;
        }

        var defaultCssTypes = ['cssText', 'cssTextShadow', 'cssBackground', 'cssPadding', 'cssMargin', 'cssBorder', 'cssRadius', 'cssShadow', 'cssSize', 'cssTransform'];

        var webSafeFonts = {
            'Arial': 'Arial',
            'Arial Black': 'Arial Black',
            'Comic Sans': 'Comic Sans',
            'Courier': 'Courier',
            'Georgia': 'Georgia',
            'Impact': 'Impact',
            'Lucida': 'Lucida Sans',
            'Lucida Console': 'Lucida Console',
            'Palatino': 'Palatino',
            'Tahoma': 'Tahoma',
            'Times New Roman': 'Times New Roman',
            'Trebuchet': 'Trebuchet',
            'Verdana': 'Verdana'
        };

        var cssTypesProperties = {
            "cssText": {
                "font-family": "", // convert
                "color": "",
                "font-size": "",
                "font-weight": "400",
                "font-style": "normal",
                "text-decoration": "none",
                "text-align": "left",
                "line-height": "",
                "letter-spacing": "normal",
            },
            "cssTextShadow": {
                "text-shadow": "none",
            },
            "cssBackground": {
                "background-color": "rgba(0, 0, 0, 0)",
                // "background-image":"", // not supported for now
                // "background-position":"",
                // "background-repeat":"",
                // "background-attachment":"",
                // "background-size":"",
            },
            "cssPadding": {
                "padding-top": "0px",
                "padding-right": "0px",
                "padding-bottom": "0px",
                "padding-left": "0px",
            },
            "cssMargin": {
                "margin-top": "0px",
                "margin-right": "0px",
                "margin-bottom": "0px",
                "margin-left": "0px",
            },
            "cssBorder": {
                "border-top": "none",
                "border-right": "none",
                "border-bottom": "none",
                "border-left": "none",
            },
            "cssRadius": {
                "border-top-left-radius": "0px",
                "border-top-right-radius": "0px",
                "border-bottom-left-radius": "0px",
                "border-bottom-right-radius": "0px",
            },
            "cssShadow": {
                "box-shadow": "none",
            },
            "cssPosition": {
                "top": "",
                "right": "",
                "bottom": "",
                "left": "",
            },
            "cssSize": {
                // "width": "0px", // not supported
                // "height": "0px",
                // "min-width": "0px",
                // "min-height": "0px",
                // "max-width": "none",
                // "max-height": "none",
            },
            "cssTransform": {
                "scale": "none",
                "translate": "none",
                "rotate": "none",
                "opacity": "1",
            },
            "cssTextAlign": {
                "text-align": "left",
            },
            "cssOpacity": {
                "opacity": "",
            }
        };

        var updatePropertyValue = function (name, value, defaultValue) {
            if (name === 'text-decoration') {
                if (value.indexOf('underline') !== -1) {
                    value = 'underline';
                }
            }
            if (name === 'font-weight') {
                if (isNaN(parseInt(value)) || parseInt(value) <= 400) {
                    value = '';
                } else {
                    value = 'bold';
                }
            }
            if (name === 'font-family') {
                var isWebSafeFont = false;
                for (var fontName in webSafeFonts) {
                    if (value.indexOf(webSafeFonts[fontName]) !== -1) {
                        value = fontName;
                        isWebSafeFont = true;
                        break;
                    }
                }
                // todo may be custom font, check for google font <link>
                if (!isWebSafeFont) {
                    value = 'googlefonts:' + value.split('"').join('');
                }
            }
            if (defaultValue !== '' && value.indexOf(defaultValue) !== -1) {
                return '';
            }
            return value;
        };

        var getValues = function (tempElement, valuesDefinition) {
            var result = {};
            element.insertBefore(tempElement, firstChild);
            for (var i = 0; i < valuesDefinition.length; i++) {
                var valueDefinition = valuesDefinition[i];
                var optionID = valueDefinition[0];
                var optionValues = {};
                var targetElement = valueDefinition[1];
                var computedStyle = getComputedStyle(targetElement);
                var cssTypes = valueDefinition[2];
                for (var j = 0; j < cssTypes.length; j++) {
                    var cssTypeProperties = cssTypesProperties[cssTypes[j]];
                    for (var propertyName in cssTypeProperties) {
                        var propertyValue = updatePropertyValue(propertyName, computedStyle.getPropertyValue(propertyName), cssTypeProperties[propertyName]);
                        if (propertyValue !== '') {
                            optionValues[propertyName] = propertyValue;
                        }
                    }
                }
                result[optionID] = JSON.stringify(optionValues);
            }
            element.removeChild(tempElement);
            return result;
        };

        var result = null;

        var createTempElement = function (className, innerHTML) {
            if (typeof innerHTML === 'undefined') {
                innerHTML = '';
            }
            var tempElement = document.createElement('div');
            tempElement.setAttribute('class', className);
            if (innerHTML !== '') {
                tempElement.innerHTML = innerHTML;
            }
            return tempElement;
        };

        var firstChildClassList = firstChild.classList;
        if (firstChildClassList.contains('bearcms-link-element')) {
            var tempElement = createTempElement('bearcms-link-element', '<a></a>');
            result = getValues(tempElement, [
                ['LinkCSS', tempElement.firstChild, defaultCssTypes],
                ['LinkContainerCSS', tempElement, ["cssPadding", "cssMargin", "cssBorder", "cssRadius", "cssShadow", "cssBackground", "cssSize", "cssTextAlign"]],
            ]);
        } else if (firstChildClassList.contains('bearcms-heading-element-large')) {
            var tempElement = createTempElement('bearcms-heading-element-large');
            result = getValues(tempElement, [
                ['HeadingCSS', tempElement, defaultCssTypes],
            ]);
        } else if (firstChildClassList.contains('bearcms-heading-element-medium')) {
            var tempElement = createTempElement('bearcms-heading-element-medium');
            result = getValues(tempElement, [
                ['HeadingCSS', tempElement, defaultCssTypes],
            ]);
        } else if (firstChildClassList.contains('bearcms-heading-element-small')) {
            var tempElement = createTempElement('bearcms-heading-element-small');
            result = getValues(tempElement, [
                ['HeadingCSS', tempElement, defaultCssTypes],
            ]);
        } else if (firstChildClassList.contains('bearcms-text-element')) {
            var tempElement = createTempElement('bearcms-text-element', '<a></a>');
            result = getValues(tempElement, [
                ['TextCSS', tempElement, defaultCssTypes],
                ['TextLinkCSS', tempElement.firstChild, ["cssText", "cssTextShadow"]],
            ]);
        } else if (firstChildClassList.contains('bearcms-image-element')) {
            var tempElement = createTempElement('bearcms-image-element');
            result = getValues(tempElement, [
                ['ImageCSS', tempElement, ["cssBorder", "cssRadius", "cssShadow"]],
            ]);
        }

        return result;
    };

    return {
        'contentChange': contentChange,
        'styleEditorChange': styleEditorChange,
        'styleEditorClose': styleEditorClose,
        'getElementDefaultStyleOptionsValues': getElementDefaultStyleOptionsValues
    };

}());