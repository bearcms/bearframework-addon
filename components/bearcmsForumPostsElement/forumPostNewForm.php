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

$form->constraints->setRequired('fptitle');
$form->constraints->setRequired('fptext');

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
            .bearcms-new-forum-post-page-text{
                display:block;
                resize: none;
            }
            .bearcms-new-forum-post-page-send-button{
                display:inline-block;
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
        echo '<label for="fptitle" class="bearcms-new-forum-post-page-title-label">'.__('bearcms.forumPosts.Title').'</label>';
        echo '<input type="text" name="fptitle" class="bearcms-new-forum-post-page-title"/><br/>';
        echo '<label for="fptext" class="bearcms-new-forum-post-page-text-label">'.__('bearcms.forumPosts.Content').'</label>';
        echo '<textarea name="fptext" class="bearcms-new-forum-post-page-text"></textarea>';
        echo '<span onclick="this.parentNode.submit();" href="javascript:void(0);" class="bearcms-new-forum-post-page-send-button">'.__('bearcms.forumPosts.Post').'</span>';
        echo '<span style="display:none;" class="bearcms-new-forum-post-page-send-button bearcms-new-forum-post-page-send-button-waiting">'.__('bearcms.forumPosts.Posting ...').'</span>';
        echo '</form>';
        echo '<script id="bearcms-bearframework-addon-script-7" src="' . htmlentities($context->assets->getUrl('components/bearcmsForumPostsElement/assets/forumPostNewForm.js', ['cacheMaxAge' => 999999999, 'version' => 1])) . '" async></script>';
        ?></body>
</html>