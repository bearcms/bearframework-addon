<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    if (!isset($data['id'])) {
        return [];
    }
    $app = App::get();
    $dataSchema = new BearCMS\DataSchema($data['id']);
    $app->hooks->execute('bearCMSDataSchemaRequested', $dataSchema);
    return $dataSchema->fields;
};
