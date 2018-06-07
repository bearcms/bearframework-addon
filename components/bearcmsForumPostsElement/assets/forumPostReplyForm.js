/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.forumPostReplyForm = (function () {

    var onBeforeSubmitForm = function (event) {
        if (typeof ivoPetkov.bearFrameworkAddons !== 'undefined' && typeof ivoPetkov.bearFrameworkAddons.users !== 'undefined') {
            var users = ivoPetkov.bearFrameworkAddons.users;
            if (!users.currentUser.exists()) {
                users.showLogin();
                event.preventDefault();
                return;
            }
        }
        var listElementID = event.target.previousSibling.firstChild.id;
        event.target.querySelector('input[type="hidden"]').value = JSON.stringify({
            'listElementID': listElementID
        });
    };

    var updateRepliesList = function (result) {
        var listElement = document.getElementById(result.listElementID);
        html5DOMDocument.insert(result.listContent);
        //temp
        listElement.innerHTML = document.body.lastChild.innerHTML;
        document.body.lastChild.parentNode.removeChild(document.body.lastChild);
    }

    var onSubmitFormDone = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.reset();
        }
        updateRepliesList(result);
    };


    var onFormRequestSent = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-forum-post-page-send-button').style.display = 'none';
        form.querySelector('.bearcms-forum-post-page-send-button-waiting').style.display = 'inline-block';
        form.querySelector('.bearcms-forum-post-page-text').setAttribute('readonly', 'readonly');
    };

    var onFormResponseReceived = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-forum-post-page-send-button').style.display = 'inline-block';
        form.querySelector('.bearcms-forum-post-page-send-button-waiting').style.display = 'none';
        form.querySelector('.bearcms-forum-post-page-text').removeAttribute('readonly');
    };

    var onFocusTextarea = function (event) {
        var form = event.target.parentNode;
        if (form.querySelector('.bearcms-forum-post-page-send-button-waiting').style.display === 'none') {
            form.querySelector('.bearcms-forum-post-page-send-button').style.display = 'inline-block';
        }
    };

    return {
        'onBeforeSubmitForm': onBeforeSubmitForm,
        'onSubmitFormDone': onSubmitFormDone,
        'onFormRequestSent': onFormRequestSent,
        'onFormResponseReceived': onFormResponseReceived,
        'onFocusTextarea': onFocusTextarea,
    }
    ;
}());