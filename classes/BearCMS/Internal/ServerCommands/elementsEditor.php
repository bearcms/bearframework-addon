<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal;

return function($data, $response) {
    if (!empty(Internal\ElementsHelper::$editorData)) {
        $requestArguments = [];
        $requestArguments['data'] = json_encode(Internal\ElementsHelper::$editorData);
        $requestArguments['jsMode'] = 1;
        $elementsEditorData = Internal\Server::call('elementseditor', $requestArguments, true);
        if (is_array($elementsEditorData) && isset($elementsEditorData['result'], $elementsEditorData['result']['content'])) {
            $response['value'] = Internal\Server::mergeAjaxResponses($response['value'], json_decode($elementsEditorData['result']['content'], true));
            $response['value'] = Internal\Server::updateAssetsUrls($response['value'], true);
        } else {
            throw new Exception('');
        }
    }
};
