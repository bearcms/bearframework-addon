<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $result = [];
    $app = App::get();
    foreach ($data as $commandData) {
        $command = $commandData['command'];
        $commandResult = [];
        if ($command === 'get') {
            $value = $app->data->getValue($commandData['key']);
            if ($value !== null) {
                $commandResult['body'] = $value;
            }
        } elseif ($command === 'set') {
            $app->data->set($app->data->make($commandData['key'], $commandData['body']));
            \BearCMS\Internal\Data::setChanged($commandData['key']);
        } elseif ($command === 'delete') {
            $app->data->delete($commandData['key']);
        } elseif ($command === 'rename') {
            $app->data->rename($commandData['sourceKey'], $commandData['targetKey']);
        } elseif ($command === 'makePublic') {
            $app->data->makePublic($commandData['key']);
        } elseif ($command === 'makePrivate') {
            $app->data->makePrivate($commandData['key']);
        }
        $result[] = $commandResult;
    }
    return $result;
};
