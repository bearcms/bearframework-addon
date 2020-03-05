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
$context = $app->contexts->get(__DIR__);

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
    $text = trim($values['cfcomment']);
    $status = 'approved';
    $cancel = false;
    $cancelMessage = '';

    if ($app->bearCMS->hasEventListeners('internalBeforeAddComment')) {
        $eventDetails = new \BearCMS\Internal\BeforeAddCommentEventDetails($author, $text, $status);
        $app->bearCMS->dispatchEvent('internalBeforeAddComment', $eventDetails);
        $author = $eventDetails->author;
        $text = $eventDetails->text;
        $status = $eventDetails->status;
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
        <link rel="client-packages-embed" name="-bearcms-comments-element-form">
        <style>
            .bearcms-comments-element-text-input{display:block;resize:none;}
            .bearcms-comments-element-send-button{cursor:pointer;}
        </style>
    </head>
    <body><?php
        $formID = 'cmntfrm' . uniqid();
        echo '<form id="' . $formID . '">';
        echo '<form-element-hidden name="cfcontext" />';
        echo '<form-element-textarea name="cfcomment" readonly="true" placeholder="' . __('bearcms.comments.Your comment') . '" class="bearcms-comments-element-text-input"/>';
        echo '<form-element-submit-button text="' . __('bearcms.comments.Send') . '" waitingText="' . __('bearcms.comments.Sending ...') . '" style="display:none;" class="bearcms-comments-element-send-button" waitingClass="bearcms-comments-element-send-button bearcms-comments-element-send-button-waiting"/>';
        echo '</form>';
        echo '<script>bearCMS.commentsElementForm.initialize("' . $formID . '",' . (int) $app->currentUser->exists() . ');</script>';
        ?></body>
</html>