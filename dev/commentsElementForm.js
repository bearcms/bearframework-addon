/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.commentsElementForm = bearCMS.commentsElementForm || (function () {

    var temp = [];

    var prepareForUserAction = function (formID) {
        var checkKey = 'ur' + formID;
        if (typeof temp[checkKey] !== 'undefined') {
            return;
        }
        temp[checkKey] = 1;
        var form = document.getElementById(formID);
        clientPackages.get('users').then(function (users) {
            users.currentUser.addEventListener('change', function () {
                updateState(formID, null);
            });
        });
        form.addEventListener('beforesubmit', onBeforeSubmit);
        form.addEventListener('submitsuccess', onSubmitSuccess);
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
            clientPackages.get('users').then(function (users) {
                users.currentUser.exists().then(function (exists) {
                    update(exists);
                });
            });
        }
    };

    var openLogin = function (event) {
        clientPackages.get('lightbox').then(function (lightbox) {
            lightbox.make();
            var formID = event.target.parentNode.parentNode.id;
            clientPackages.get('users').then(function (users) {
                prepareForUserAction(formID);
                users.openLogin();
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
            clientPackages.get('-bearcms-html5domdocument').then(function (html5DOMDocument) {
                var listElement = document.getElementById(result.listElementID);
                html5DOMDocument.insert(result.listContent, [listElement, 'outerHTML']);
            });
        }
    };

    var initialize = function (formID, hasUser) {
        updateState(formID, hasUser);
        if (hasUser) {
            prepareForUserAction(formID);
        }
    };

    return {
        'initialize': initialize
    };

}());