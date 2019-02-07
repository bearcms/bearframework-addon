<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;
use BearCMS\Internal;

$app = App::get();
$context = $app->contexts->get(__FILE__);

$form->constraints->setRequired('cfcomment', __('bearcms.comments.Your comment cannot be empty!'));

$form->onSubmit = function($values) use ($component, $app, $context) {
    $contextData = json_decode($values['cfcontext'], true);
    if (is_array($contextData) && isset($contextData['listElementID'], $contextData['listCommentsCount'])) {
        $listElementID = (string) $contextData['listElementID'];
        $listCommentsCount = (int) $contextData['listCommentsCount'];
    } else {
        $this->throwError();
    }
    if (!$app->currentUser->exists()) {
        $this->throwError();
    }

    $threadID = $component->threadID;
    $author = [
        'type' => 'user',
        'provider' => $app->currentUser->provider,
        'id' => $app->currentUser->id
    ];
    $text = $values['cfcomment'];
    $status = 'approved';
    $cancel = false;
    $cancelMessage = '';

    if ($app->bearCMS->hasEventListeners('internalBeforeAddComment')) {
        $eventDetails = new \BearCMS\Internal\BeforeAddCommentEventDetails($author, $text, $status);
        $app->bearCMS->dispatchEvent('internalBeforeAddComment');
        $cancel = $eventDetails->cancel;
        $cancelMessage = $eventDetails->cancelMessage;
    }
    if ($cancel) {
        $this->throwError($cancelMessage);
    }
    Internal\Data\Comments::add($threadID, $author, $text, $status);

    $listContent = $app->components->process('<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($listCommentsCount) . '" threadID="' . htmlentities($threadID) . '" />');
    return [
        'listElementID' => $listElementID,
        'listContent' => $listContent,
        'success' => 1
    ];
};
?><html>
    <head>
        <style>
            .bearcms-comments-element-text-input{display:block;resize:none;}
            .bearcms-comments-element-send-button{cursor:pointer;}
        </style>
    </head>
    <body><?php
        echo '<form'
        . ' onbeforesubmit="bearCMS.commentsElement.onBeforeSubmitForm(event);"'
        . ' onsubmitdone="bearCMS.commentsElement.onSubmitFormDone(event);"'
        . ' onrequestsent="bearCMS.commentsElement.onFormRequestSent(event);"'
        . ' onresponsereceived="bearCMS.commentsElement.onFormResponseReceived(event);"'
        . '>';
        echo '<input type="hidden" name="cfcontext"/>';
        echo '<textarea placeholder="' . __('bearcms.comments.Your comment') . '" name="cfcomment" class="bearcms-comments-element-text-input" onfocus="bearCMS.commentsElement.onFocusTextarea(event);"></textarea>';
        echo '<span onclick="this.parentNode.submit();" class="bearcms-comments-element-send-button" style="display:none;">' . __('bearcms.comments.Send') . '</span>';
        echo '<span class="bearcms-comments-element-send-button bearcms-comments-element-send-button-waiting" style="display:none;">' . __('bearcms.comments.Sending ...') . '</span>';
        echo '</form>';
        ?></body>
</html>