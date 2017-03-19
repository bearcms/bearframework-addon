<?php
/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use \BearFramework\App;

$app = App::get();
$context = $app->context->get(__FILE__);

$form->constraints->setRequired('fprtext', 'Your reply cannot be empty!');

$form->onSubmit = function($values) use ($component, $app, $context) {
    $contextData = json_decode($values['fprcontext'], true);
    if (is_array($contextData) && isset($contextData['listElementID'])) {
        $listElementID = (string) $contextData['listElementID'];
    } else {
        $this->throwError();
    }
    if (!$app->currentUser->exists()) {
        $this->throwError();
    }

    $forumPostID = $component->forumPostID;
    $author = [
        'type' => 'user',
        'provider' => $app->currentUser->provider,
        'id' => $app->currentUser->id
    ];

    $data = new ArrayObject();
    $data->author = $author;
    $data->text = $values['fprtext'];
    $data->cancel = false;
    $data->cancelMessage = '';
    $data->status = 'approved';
    $app->hooks->execute('bearCMSForumPostReplyAdd', $data);
    if ($data->cancel) {
        $this->throwError($data->cancelMessage);
    }
    \BearCMS\Internal\Data\ForumPostsReplies::add($forumPostID, $author, $values['fprtext'], $data->status);

    $listContent = $app->components->process('<component src="file:' . $context->dir . '/components/bearcmsForumPostsElement/forumPostRepliesList.php" forumPostID="' . htmlentities($forumPostID) . '" />');
    return [
        'listElementID' => $listElementID,
        'listContent' => $listContent,
        'success' => 1
    ];
};
?><html>
    <head>
        <style>
            .bearcms-forum-post-page-text{
                display:block;
                resize: none;
            }
            .bearcms-forum-post-page-send-button{
                display:inline-block;
                cursor: pointer;
                display:none;
            }
        </style>
    </head>
    <body><?php
        echo '<form'
        . ' onbeforesubmit="bearCMS.forumPostReplyForm.onBeforeSubmitForm(event);"'
        . ' onsubmitdone="bearCMS.forumPostReplyForm.onSubmitFormDone(event);"'
        . ' onrequestsent="bearCMS.forumPostReplyForm.onFormRequestSent(event);"'
        . ' onresponsereceived="bearCMS.forumPostReplyForm.onFormResponseReceived(event);"'
        . '>';
        echo '<input type="hidden" name="fprcontext"/>';
        echo '<textarea name="fprtext" class="bearcms-forum-post-page-text" onfocus="bearCMS.forumPostReplyForm.onFocusTextarea(event);"></textarea>';
        echo '<span onclick="this.parentNode.submit();" href="javascript:void(0);" class="bearcms-forum-post-page-send-button">Send</span>';
        echo '<span style="display:none;" class="bearcms-forum-post-page-send-button bearcms-forum-post-page-send-button-waiting">Sending ...</span>';
        echo '</form>';
        echo '<script src="' . htmlentities($context->assets->getUrl('components/bearcmsForumPostsElement/assets/forumPostReplyForm.js', ['cacheMaxAge' => 999999, 'version' => 1])) . '"></script>';
        ?></body>
</html>