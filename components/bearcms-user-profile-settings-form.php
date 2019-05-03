<?php

use BearFramework\App;

$app = App::get();
$providerID = 'bearcms';
$userID = $app->currentUser->id;

$getUserData = function() use ($app, $providerID, $userID) {
    $data = $app->users->getUserData($providerID, $userID);
    if (empty($data)) {
        $data = [];
    }
    if (!isset($data['image'])) {
        $data['image'] = '';
    }
    if (!isset($data['name'])) {
        $data['name'] = '';
    }
    if (!isset($data['description'])) {
        $data['description'] = '';
    }
    return $data;
};

$form->onSubmit = function($values) use ($app, $providerID, $userID, $getUserData) {
    $data = $getUserData();
    $data['name'] = isset($values['name']) ? trim((string) $values['name']) : '';
    $data['description'] = isset($values['description']) ? trim((string) $values['description']) : '';

    $removeOldImageIfExists = isset($values['image']) && strlen($values['image']) === 0;

    $newImageKey = null;
    if (isset($values['image_files'])) {
        $files = json_decode($values['image_files'], true);
        if (isset($files[0])) {
            $newImageKey = $app->users->saveUserFile($providerID, $files[0]['filename'], pathinfo($files[0]['value'], PATHINFO_EXTENSION));
            $removeOldImageIfExists = true;
        }
    }
    if ($removeOldImageIfExists && strlen($data['image']) > 0) {
        $app->users->deleteUserFile($providerID, $data['image']);
        $data['image'] = '';
    }
    if ($newImageKey !== null) {
        $data['image'] = $newImageKey;
    }

    $app->users->saveUserData($providerID, $userID, $data);
};

$data = $getUserData();

echo '<form onsubmitsuccess="window.location.reload();">';
echo '<form-element-image name="image" label="' . htmlentities(__('bearcms.users.settings.image')) . '" value="' . htmlentities(strlen($data['image']) > 0 ? 'image.jpg' : '') . '" valuePreviewUrl="' . htmlentities(strlen($data['image']) > 0 ? $app->currentUser->getImageUrl(500) : '') . '" />';
echo '<form-element-textbox name="name" label="' . htmlentities(__('bearcms.users.settings.name')) . '" value="' . htmlentities($data['name']) . '" />';
echo '<form-element-textarea name="description" label="' . htmlentities(__('bearcms.users.settings.description')) . '" value="' . htmlentities($data['description']) . '" />';
echo '<form-element-submit-button text="' . htmlentities(__('bearcms.users.settings.save')) . '" waitingText="' . htmlentities(__('bearcms.users.settings.saving')) . '" />';
echo '</form>';
