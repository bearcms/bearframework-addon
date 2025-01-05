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

$allowFilesUpload = (string)$component->allowFilesUpload === 'true';

$form->onSubmit = function ($values) use ($component, $app, $context, $allowFilesUpload) {

    if (!$app->rateLimiter->logIP('bearcms-comments-form', ['4/m', '40/h'])) {
        $this->throwError(__('bearcms.comments.tooMany'));
    }

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

    $files = [];
    if ($allowFilesUpload) {
        if (isset($values['cffiles'])) {
            $filesData = json_decode($values['cffiles'], true);
            if (is_array($filesData)) {
                foreach ($filesData as $fileData) {
                    if (isset($fileData['filename'])) {
                        $files[] = ['name' => $fileData['value'], 'filename' => $fileData['filename']];
                    }
                }
            }
        }
    }

    if (strlen($text) === 0 && empty($files)) {
        $this->throwElementError('cfcomment', __('bearcms.comments.Your comment cannot be empty!'));
    }

    if ($app->bearCMS->hasEventListeners('internalBeforeAddComment')) {
        $eventDetails = new \BearCMS\Internal\BeforeAddCommentEventDetails($threadID, $author, $text, $status, $files);
        $app->bearCMS->dispatchEvent('internalBeforeAddComment', $eventDetails);
        $author = $eventDetails->author;
        $text = $eventDetails->text;
        $files = $eventDetails->files;
        $status = $eventDetails->status;
        $cancel = $eventDetails->cancel;
        $cancelMessage = $eventDetails->cancelMessage;
    }
    if ($cancel) {
        $this->throwError($cancelMessage);
    }
    Internal\Data\Comments::add($threadID, $author, $text, $status, $files);

    $listContent = $app->components->process('<component src="file:' . $context->dir . '/components/bearcmsCommentsElement/commentsList.php" count="' . htmlentities($listCommentsCount) . '" threadID="' . htmlentities($threadID) . '" />');
    return [
        'listElementID' => $listElementID,
        'listContent' => $listContent,
        'success' => 1
    ];
};

echo '<html><head>';
echo '<link rel="client-packages-embed" name="-bearcms-comments-element-form">';
echo '<style>';
echo '.bearcms-comments-element-text-input{display:block;resize:none;}';
echo '.bearcms-comments-element-send-button{cursor:pointer;}';
echo '.bearcms-comments-element [data-form-element-type="submit-button"]{display:flex;}';
echo '</style></head><body>';
$formID = 'cmntfrm' . uniqid();
echo '<form id="' . $formID . '" class="bearcms-comments-element-form">';
echo '<form-element-hidden name="cfcontext" />';
echo '<form-element-textarea name="cfcomment" readonly="true" placeholder="' . __('bearcms.comments.Your comment') . '" class="bearcms-comments-element-text-input"/>';
if ($allowFilesUpload) {
    echo '<form-element-file name="cffiles" multiple="true" class="bearcms-comments-element-files-input"/>';
}
echo '<form-element-submit-button text="' . __('bearcms.comments.Send') . '" waitingText="' . __('bearcms.comments.Sending ...') . '" style="display:none;" class="bearcms-comments-element-send-button" waitingClass="bearcms-comments-element-send-button bearcms-comments-element-send-button-waiting"/>';
echo '</form>';
echo '<script>bearCMS.commentsElementForm.initialize("' . $formID . '",' . (int) $app->currentUser->exists() . ');</script>';
echo '</body></html>';
