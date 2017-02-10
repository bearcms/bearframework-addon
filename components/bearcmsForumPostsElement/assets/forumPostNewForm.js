/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};

bearCMS.forumPostNewForm = (function () {

    var onBeforeSubmitForm = function (event) {
        var users = ivoPetkov.bearFrameworkAddons.users;
        if (!users.currentUser.exists()) {
            users.showLogin();
            event.preventDefault();
            return;
        }
    };

//    var updateCommentsList = function (result) {
//        var listElement = document.getElementById(result.listElementID);
//        html5DOMDocument.insert(result.listContent);
//        //temp
//        listElement.innerHTML = document.body.lastChild.innerHTML;
//        listElement.setAttribute('data-count', document.body.lastChild.getAttribute('data-count'));
//        document.body.lastChild.parentNode.removeChild(document.body.lastChild);
//    }

    var onSubmitFormDone = function (event) {
        var form = event.target;
        var result = event.result;
        if (typeof result.success !== 'undefined') {
            form.reset();
        }
        if (typeof result.redirectUrl !== 'undefined') {
            window.location = result.redirectUrl;
        }
        //updateCommentsList(result);
    };

    return {
        'onBeforeSubmitForm': onBeforeSubmitForm,
        'onSubmitFormDone': onSubmitFormDone
    };

}());