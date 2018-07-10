<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\ElementsHelper;
use BearCMS\Internal\Server;

return function($data, $response) {
    if (!empty(ElementsHelper::$editorData)) {
        $requestArguments = [];
        $requestArguments['data'] = json_encode(ElementsHelper::$editorData);
        $requestArguments['jsMode'] = 1;
        $elementsEditorData = Server::call('elementseditor', $requestArguments, true);
        if (is_array($elementsEditorData) && isset($elementsEditorData['result'], $elementsEditorData['result']['content'])) {
            $response['value'] = Server::mergeAjaxResponses($response['value'], json_decode($elementsEditorData['result']['content'], true));
            $response['value'] = Server::updateAssetsUrls($response['value'], true);
        } else {
            throw new Exception('');
        }
    }
};
