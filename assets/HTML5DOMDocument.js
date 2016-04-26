html5DOMDocument = {};
html5DOMDocument.insert = function (code, target) {
    var element = document.createElement('html');
    //code = '<html><head><style>*{border:2px solid red}</style><link rel="stylesheet" href="http://all.projects/playground/style.css"></link></head><body>AAAAAAAAAA<script>alert(1);</script><script src="http://all.projects/playground/alert.js"></script></body></html>';
    element.innerHTML = code;

    html5DOMDocument.prepare(element);

    var headElements = element.querySelectorAll('head');
    var headElementsCount = headElements.length;
    for (var i = 0; i < headElementsCount; i++) {
        document.head.insertAdjacentHTML('beforeend', headElements[i].innerHTML);
    }

    var bodyElements = element.querySelectorAll('body');
    var bodyElementsCount = bodyElements.length;
    for (var i = 0; i < bodyElementsCount; i++) {
        document.body.insertAdjacentHTML('beforeend', bodyElements[i].innerHTML);
    }

    html5DOMDocument.execute(document);
};

html5DOMDocument.prepare = function (element) {
    var scripts = element.querySelectorAll('script');
    var scriptsCount = scripts.length;
    for (var i = 0; i < scriptsCount; i++) {
        scripts[i].setAttribute('data-html-magic-script', '1');
    }
};

html5DOMDocument.execute = function (element) {
    var scriptsToExecute = element.querySelectorAll('[data-html-magic-script]');
    var scriptsToExecuteCount = scriptsToExecute.length;
    for (var i = 0; i < scriptsToExecuteCount; i++) {
        var scriptToExecute = scriptsToExecute[i];
        var newScriptTag = document.createElement('script');
        var type = scriptToExecute.getAttribute('type');
        if (type !== null) {
            newScriptTag.setAttribute("type", type);
        }
        var src = scriptToExecute.getAttribute('src');
        if (src !== null) {
            newScriptTag.setAttribute("src", src);
        }
        newScriptTag.innerHTML = scriptToExecute.innerHTML;
        scriptToExecute.parentNode.insertBefore(newScriptTag, scriptToExecute);
        scriptToExecute.parentNode.removeChild(scriptToExecute);
    }
};

html5DOMDocument.evalElement = function (element) {
    html5DOMDocument.prepare(element);
    html5DOMDocument.execute(element);
};