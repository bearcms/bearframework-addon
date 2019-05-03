/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};
bearCMS.commentsElement = bearCMS.commentsElement || (function () {

    var showUserLoginIfNeeded = function (event) {
        clientShortcuts.get('users').then(function (users) {
            if (!users.currentUser.exists()) {
                //event.preventDefault(); // embed users
                users.openLogin();
                //return true;
            }
        });
        return false;
    };

    var updateCommentsList = function (result, callback) {
        clientShortcuts.get('-bearcms-html5domdocument').then(function (html5DOMDocument) {
            var listElement = document.getElementById(result.listElementID);
            html5DOMDocument.insert(result.listContent, [listElement, 'outerHTML']);
            callback();
        });
    };

    var loadMore = function (event, data) {
        event.target.innerHTML += " ...";
        var listElementID = event.target.parentNode.parentNode.id;
        var listCommentsCount = parseInt(event.target.parentNode.parentNode.getAttribute('data-count'), 10) + 10;
        var requestData = [];
        requestData['serverData'] = data['serverData'];
        requestData['listElementID'] = listElementID;
        requestData['listCommentsCount'] = listCommentsCount;
        clientShortcuts.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-comments-load-more', requestData).then(function (response) {
                var result = JSON.parse(response);
                updateCommentsList(result, function () {});
            });
        });
    };

    var onBeforeSubmitForm = function (form) {
        var listElementID = form.previousSibling.id;
        var listCommentsCount = form.previousSibling.getAttribute('data-count');
        form.querySelector('input[type="hidden"]').value = JSON.stringify({
            'listElementID': listElementID,
            'listCommentsCount': listCommentsCount
        });
        if (typeof form.bearCMSCommentsEventsAttached === 'undefined') {
            form.bearCMSCommentsEventsAttached = true;
            form.addEventListener('submitstart', onSubmitStart);
            form.addEventListener('submitsuccess', onSubmitSuccess);
            form.addEventListener('submiterror', onSubmitError);
        }
    };

    var onSubmitSuccess = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.noUser !== 'undefined') {
            onSubmitEnd(event);
            showUserLoginIfNeeded();
            return;
        }
        if (typeof result.success !== 'undefined') {
            updateCommentsList(result, function () {
                onSubmitEnd(event);
                form.reset();
            });
        }
    };

    var onSubmitError = function (event) {
        onSubmitEnd(event);
    };

    var onSubmitStart = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-comments-element-send-button').style.display = 'none';
        form.querySelector('.bearcms-comments-element-send-button-waiting').style.removeProperty('display');
        form.querySelector('.bearcms-comments-element-text-input').setAttribute('readonly', 'readonly');
    };

    var onSubmitEnd = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-comments-element-send-button').style.removeProperty('display');
        form.querySelector('.bearcms-comments-element-send-button-waiting').style.display = 'none';
        form.querySelector('.bearcms-comments-element-text-input').removeAttribute('readonly');
    };

    var onFocusTextarea = function (event) {
        var form = event.target.parentNode;
        if (form.querySelector('.bearcms-comments-element-send-button-waiting').style.display === 'none') {
            form.querySelector('.bearcms-comments-element-send-button').style.removeProperty('display');
        }
    };

    return {
        'loadMore': loadMore,
        'onBeforeSubmitForm': onBeforeSubmitForm,
        'onFocusTextarea': onFocusTextarea
    };

}());