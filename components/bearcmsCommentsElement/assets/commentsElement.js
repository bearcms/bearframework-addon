/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.commentsElement = (function () {

    var showUserLoginIfNeeded = function (event) {
        if (typeof ivoPetkov.bearFrameworkAddons !== 'undefined' && typeof ivoPetkov.bearFrameworkAddons.users !== 'undefined') {
            var users = ivoPetkov.bearFrameworkAddons.users;
            if (!users.currentUser.exists()) {
                users.showLogin();
                event.preventDefault();
                return true;
            }
        }
        return false;
    };

    var onBeforeSubmitForm = function (event) {
        if (showUserLoginIfNeeded(event)) {
            return;
        }
        var listElementID = event.target.previousSibling.id;
        var listCommentsCount = event.target.previousSibling.getAttribute('data-count');
        event.target.querySelector('input[type="hidden"]').value = JSON.stringify({
            'listElementID': listElementID,
            'listCommentsCount': listCommentsCount
        });
    };

    var updateCommentsList = function (result) {
        var listElement = document.getElementById(result.listElementID);
        html5DOMDocument.insert(result.listContent);
        //temp
        listElement.innerHTML = document.body.lastChild.innerHTML;
        listElement.setAttribute('data-count', document.body.lastChild.getAttribute('data-count'));
        document.body.lastChild.parentNode.removeChild(document.body.lastChild);
    };

    var loadMore = function (event, data) {
        var listElementID = event.target.parentNode.parentNode.id;
        var listCommentsCount = parseInt(event.target.parentNode.parentNode.getAttribute('data-count'), 10) + 10;
        var requestData = [];
        requestData['serverData'] = data['serverData'];
        requestData['listElementID'] = listElementID;
        requestData['listCommentsCount'] = listCommentsCount;
        ivoPetkov.bearFrameworkAddons.serverRequests.send('bearcms-comments-load-more', requestData, function (response) {
            var result = JSON.parse(response);
            updateCommentsList(result);
        });
    };

    var onSubmitFormDone = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.reset();
        }
        updateCommentsList(result);
    };

    var onFormRequestSent = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-comments-element-send-button').style.display = 'none';
        form.querySelector('.bearcms-comments-element-send-button-waiting').style.removeProperty('display');
        form.querySelector('.bearcms-comments-element-text-input').setAttribute('readonly', 'readonly');
    };

    var onFormResponseReceived = function (event) {
        var form = event.target;
        form.querySelector('.bearcms-comments-element-send-button').style.removeProperty('display');
        form.querySelector('.bearcms-comments-element-send-button-waiting').style.display = 'none';
        form.querySelector('.bearcms-comments-element-text-input').removeAttribute('readonly');
    };

    var onFocusTextarea = function (event) {
        if (showUserLoginIfNeeded(event)) {
            event.target.blur();
        }
        var form = event.target.parentNode;
        if (form.querySelector('.bearcms-comments-element-send-button-waiting').style.display === 'none') {
            form.querySelector('.bearcms-comments-element-send-button').style.removeProperty('display');
        }
    };

    return {
        'loadMore': loadMore,
        'onBeforeSubmitForm': onBeforeSubmitForm,
        'onSubmitFormDone': onSubmitFormDone,
        'onFormRequestSent': onFormRequestSent,
        'onFormResponseReceived': onFormResponseReceived,
        'onFocusTextarea': onFocusTextarea
    };

}());