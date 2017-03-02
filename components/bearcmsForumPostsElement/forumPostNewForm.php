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

$form->constraints->setRequired('fptitle', 'Your title cannot be empty!');
$form->constraints->setRequired('fptext', 'Your comment cannot be empty!');

$form->onSubmit = function($values) use ($component, $app) {

    $categoryID = $component->categoryID;

    if (!$app->currentUser->exists()) {
        $this->throwError();
    }

    $author = [
        'type' => 'user',
        'provider' => $app->currentUser->provider,
        'id' => $app->currentUser->id
    ];

    $data = new ArrayObject();
    $data->author = $author;
    $data->title = $values['fptitle'];
    $data->text = $values['fptext'];
    $data->cancel = false;
    $data->cancelMessage = '';
    $data->status = 'approved';
    $app->hooks->execute('bearCMSForumPostAdd', $data);
    if ($data->cancel) {
        $this->throwError($data->cancelMessage);
    }
    $forumPostID = \BearCMS\Internal\Data\ForumPosts::add($categoryID, $author, $values['fptitle'], $values['fptext'], $data->status);

    $slug = $forumPostID; //todo
    return [
        'success' => 1,
        'redirectUrl' => $app->urls->get('/f/' . $slug . '/' . $forumPostID . '/')
    ];
};
?><html>
    <head>
        <style>
            .bearcms-forum-post-form-element-textarea{
                display:block;
                width:100%;
                resize: none;
                box-sizing: border-box;
                height:100px;
                padding:20px;
            }
            .bearcms-forum-post-form-element-send-button{
                background-color:gray;
                display:inline-block;
                padding:10px;

                margin-top: 15px;
                cursor: pointer;
            }
        </style>
    </head>
    <body><?php
        echo '<form'
        . ' onbeforesubmit="bearCMS.forumPostNewForm.onBeforeSubmitForm(event);"'
        . ' onsubmitdone="bearCMS.forumPostNewForm.onSubmitFormDone(event);"'
        . ' onrequestsent="bearCMS.forumPostNewForm.onFormRequestSent(event);"'
        . ' onresponsereceived="bearCMS.forumPostNewForm.onFormResponseReceived(event);"'
        . '>';
        echo '<label for="fptitle">Title</label>';
        echo '<input type="text" name="fptitle" class="bearcms-forum-post-form-element-title"/><br/>';
        echo '<label for="fptext">Content</label>';
        echo '<textarea name="fptext" class="bearcms-forum-post-form-element-textarea"></textarea>';
        echo '<span onclick="this.parentNode.submit();" href="javascript:void(0);" class="bearcms-forum-post-form-element-send-button">Post</span>';
        echo '<span style="display:none;" class="bearcms-forum-post-form-element-send-button bearcms-forum-post-form-element-send-button-waiting">Posting ...</span>';
        echo '</form>';
        echo '<script src="' . htmlentities($context->assets->getUrl('components/bearcmsForumPostsElement/assets/forumPostNewForm.js')) . '"></script>';
        ?></body>
</html>