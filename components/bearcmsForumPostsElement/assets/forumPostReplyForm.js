/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.forumPostReplyForm = (function () {

    var onBeforeSubmitForm = function (event) {
        var users = ivoPetkov.bearFrameworkAddons.users;
        if (!users.currentUser.exists()) {
            users.showLogin();
            event.preventDefault();
            return;
        }
        var listElementID = event.target.previousSibling.id;
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

    return {
        'onBeforeSubmitForm': onBeforeSubmitForm,
        'onSubmitFormDone': onSubmitFormDone
    };

}());