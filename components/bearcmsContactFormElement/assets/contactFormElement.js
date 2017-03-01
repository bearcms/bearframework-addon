/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.contactFormElement = (function () {

    var onRequestSent = function (event) {
        var form = event.target;
        form.childNodes[form.childNodes.length - 2].style.display = 'none';
        form.childNodes[form.childNodes.length - 1].style.display = 'inline-block';
    };
    
    var onResponseReceived = function (event) {
        var form = event.target;
        form.childNodes[form.childNodes.length - 2].style.display = 'inline-block';
        form.childNodes[form.childNodes.length - 1].style.display = 'none';
    };

    var onSubmitDone = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.reset();
        }
    };

    return {
        'onRequestSent': onRequestSent,
        'onResponseReceived': onResponseReceived,
        'onSubmitDone': onSubmitDone
    };

}());