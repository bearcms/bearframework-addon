/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.contactFormElement = (function () {

    var onRequestSent = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-contact-form-element-send-button').style.display = 'none';
        form.querySelector('.bearcms-contact-form-element-send-button-waiting').style.display = 'inline-block';
        form.querySelector('.bearcms-contact-form-element-email').setAttribute('readonly', 'readonly');
        form.querySelector('.bearcms-contact-form-element-message').setAttribute('readonly', 'readonly');
    };

    var onResponseReceived = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-contact-form-element-send-button').style.display = 'inline-block';
        form.querySelector('.bearcms-contact-form-element-send-button-waiting').style.display = 'none';
        form.querySelector('.bearcms-contact-form-element-email').removeAttribute('readonly');
        form.querySelector('.bearcms-contact-form-element-message').removeAttribute('readonly');
    };

    var onSubmitDone = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.reset();
            if (result.message.length > 0) {
                alert(result.message);
            }
        }
    };

    return {
        'onRequestSent': onRequestSent,
        'onResponseReceived': onResponseReceived,
        'onSubmitDone': onSubmitDone
    };

}());