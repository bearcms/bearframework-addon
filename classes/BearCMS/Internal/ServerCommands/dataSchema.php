<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    if (!isset($data['id'])) {
        return [];
    }
    $app = App::get();
    $dataSchema = new BearCMS\Internal\DataSchema($data['id']);
    $app->hooks->execute('bearCMSDataSchemaRequested', $dataSchema);
    return $dataSchema->fields;
};
