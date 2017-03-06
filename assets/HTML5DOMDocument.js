/*
 * HTML5 DOM Document JS
 * http://ivopetkov.com/
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

html5DOMDocument = (function () {

    var executionsCounter = 0;

    var prepare = function (element, counter) {
        var scripts = element.querySelectorAll('script');
        var scriptsCount = scripts.length;
        for (var i = 0; i < scriptsCount; i++) {
            scripts[i].setAttribute('data-html5-dom-document-script-' + counter, '1');
        }
    };

    var execute = function (element, counter) {
        var scriptsToExecute = element.querySelectorAll('[data-html5-dom-document-script-' + counter + ']');
        var scriptsToExecuteCount = scriptsToExecute.length;
        for (var i = 0; i < scriptsToExecuteCount; i++) {
            var breakAfterThisScript = false;
            var scriptToExecute = scriptsToExecute[i];
            scriptToExecute.removeAttribute('data-html5-dom-document-script-' + counter);
            var newScriptTag = document.createElement('script');
            var type = scriptToExecute.getAttribute('type');
            if (type !== null) {
                newScriptTag.setAttribute("type", type);
            }
            var src = scriptToExecute.getAttribute('src');
            if (src !== null) {
                newScriptTag.setAttribute("src", src);
                if ((typeof scriptToExecute.async === 'undefined' || scriptToExecute.async === false) && i + 1 < scriptsToExecuteCount) {
                    breakAfterThisScript = true;
                    newScriptTag.addEventListener('load', function () {
                        execute(element, counter);
                    });
                }
            }
            newScriptTag.innerHTML = scriptToExecute.innerHTML;
            scriptToExecute.parentNode.insertBefore(newScriptTag, scriptToExecute);
            scriptToExecute.parentNode.removeChild(scriptToExecute);
            if (breakAfterThisScript) {
                break;
            }
        }
    };

    /**
     * 
     * @param string code
     * @param string target Available values: afterBodyBegin, beforeBodyEnd, [element, innerHTML], [element, outerHTML], [element, beforeBegin], [element, afterBegin], [element, beforeEnd], [element, afterEnd]
     */
    var insert = function (code, target) {
        if (typeof target === 'undefined') {
            target = 'beforeBodyEnd';
        }

        executionsCounter++;
        var element = document.createElement('html');
        element.innerHTML = code;

        prepare(element, executionsCounter);

        var headElements = element.querySelectorAll('head');
        var headElementsCount = headElements.length;
        for (var i = 0; i < headElementsCount; i++) {
            document.head.insertAdjacentHTML('beforeend', headElements[i].innerHTML);
        }

        var bodyElements = element.querySelectorAll('body');
        var bodyElementsCount = bodyElements.length;
        for (var i = 0; i < bodyElementsCount; i++) {
            if (target === 'afterBodyBegin') {
                document.body.insertAdjacentHTML('afterbegin', bodyElements[i].innerHTML);
            } else if (target === 'beforeBodyEnd') {
                document.body.insertAdjacentHTML('beforeend', bodyElements[i].innerHTML);
            } else if (typeof target === 'object' && typeof target[0] !== 'undefined') {
                if (typeof target[1] === 'undefined') {
                    target[1] = 'innerHTML';
                }
                if (target[1] === 'innerHTML') {
                    target[0].innerHTML = bodyElements[i].innerHTML;
                } else if (target[1] === 'outerHTML') {
                    target[0].outerHTML = bodyElements[i].innerHTML;
                } else if (target[1] === 'beforeBegin') {
                    target[0].insertAdjacentHTML('beforebegin', bodyElements[i].innerHTML);
                } else if (target[1] === 'afterBegin') {
                    target[0].insertAdjacentHTML('afterend', bodyElements[i].innerHTML);
                } else if (target[1] === 'beforeEnd') {
                    target[0].insertAdjacentHTML('beforeend', bodyElements[i].innerHTML);
                } else if (target[1] === 'afterEnd') {
                    target[0].insertAdjacentHTML('afterend', bodyElements[i].innerHTML);
                }
            }
        }

        execute(document, executionsCounter);
    };

    var evalElement = function (element) {
        executionsCounter++;
        prepare(element, executionsCounter);
        execute(element, executionsCounter);
    };

    return {
        'insert': insert,
        'evalElement': evalElement
    };

}());
