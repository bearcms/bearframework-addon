/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientShortcuts */

var bearCMS = bearCMS || {};
bearCMS.commentsElement = bearCMS.commentsElement || (function () {

    var temp = [];

    var prepareForUserAction = function (formID) {
        var checkKey = 'ur' + formID;
        if (typeof temp[checkKey] !== 'undefined') {
            return;
        }
        temp[checkKey] = 1;
        var form = document.getElementById(formID);
        clientShortcuts.get('users').then(function (users) {
            users.currentUser.addEventListener('change', function () {
                updateState(formID, null);
            });
        });
        form.addEventListener('beforesubmit', onBeforeSubmit);
        form.addEventListener('submitsuccess', onSubmitSuccess);
    };

    var initializeForm = function (formID, hasUser) {
        updateState(formID, hasUser);
        if (hasUser) {
            return prepareForUserAction(formID); // return is neededed because of bug in closure compiler
        }
    };

    var updateState = function (formID, hasUser) {
        var update = function (hasCurrentUser) {
            var form = document.getElementById(formID);
            var textarea = form.querySelector('textarea');
            if (hasCurrentUser) {
                textarea.removeAttribute('readonly');
                textarea.style.cursor = "auto";
                textarea.removeEventListener('click', openLogin);
                form.querySelector('.bearcms-comments-element-send-button').style.removeProperty('display');
            } else {
                textarea.setAttribute('readonly', true);
                textarea.style.cursor = "pointer";
                textarea.addEventListener('click', openLogin);
                form.querySelector('.bearcms-comments-element-send-button').style.display = 'none';
            }
        };
        if (hasUser !== null) {
            update(hasUser);
        } else {
            clientShortcuts.get('users').then(function (users) {
                update(users.currentUser.exists());
            });
        }
    };

    var openLogin = function (event) {
        var formID = event.target.parentNode.parentNode.id;
        clientShortcuts.get('users').then(function (users) {
            prepareForUserAction(formID);
            users.openLogin();
        });
    };

    var updateCommentsList = function (result) {
        clientShortcuts.get('-bearcms-html5domdocument').then(function (html5DOMDocument) {
            var listElement = document.getElementById(result.listElementID);
            html5DOMDocument.insert(result.listContent, [listElement, 'outerHTML']);
        });
    };

    var loadMore = function (button, data) {
        button.innerHTML += " ...";
        var elementContainer = button.parentNode.parentNode;
        var requestData = {
            'serverData': data['serverData'],
            'listElementID': elementContainer.id,
            'listCommentsCount': parseInt(elementContainer.getAttribute('data-count'), 10) + 10
        };
        clientShortcuts.get('serverRequests').then(function (serverRequests) {
            serverRequests.send('bearcms-comments-load-more', requestData).then(function (response) {
                var result = JSON.parse(response);
                updateCommentsList(result);
            });
        });
    };

    var onBeforeSubmit = function (event) {
        var form = event.target;
        var elementContainer = form.previousSibling;
        form.querySelector('[name="cfcontext"]').value = JSON.stringify({
            'listElementID': elementContainer.id,
            'listCommentsCount': elementContainer.getAttribute('data-count')
        });
    };

    var onSubmitSuccess = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.querySelector('[name="cfcomment"]').value = '';
            updateCommentsList(result);
        }
    };

    return {
        'initializeForm': initializeForm,
        'loadMore': loadMore
    };

}());