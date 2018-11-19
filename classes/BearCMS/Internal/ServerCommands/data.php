<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $result = [];
    $app = App::get();

    $validateKey = function($key) {
        if (strpos($key, 'bearcms/') !== 0 && strpos($key, '.temp/bearcms/') !== 0 && strpos($key, '.recyclebin/bearcms/') !== 0) {
            throw new \Exception('The key ' . $key . ' is forbidden!');
        }
    };

    foreach ($data as $commandData) {
        $command = $commandData['command'];
        $commandResult = [];
        if ($command === 'get') {
            $validateKey($commandData['key']);
            $value = $app->data->getValue($commandData['key']);
            $commandResult['schemaVersion'] = 2;
            if ($value !== null) {
                $commandResult['result'] = ['exists' => true, 'value' => $value];
            } else {
                $commandResult['result'] = ['exists' => false];
            }
        } elseif ($command === 'set') {
            $validateKey($commandData['key']);
            $app->data->set($app->data->make($commandData['key'], $commandData['body']));
            \BearCMS\Internal\Data::setChanged($commandData['key']);
        } elseif ($command === 'delete') {
            $validateKey($commandData['key']);
            if ($app->data->exists($commandData['key'])) {
                $app->data->delete($commandData['key']);
            }
        } elseif ($command === 'rename') {
            $validateKey($commandData['sourceKey']);
            $validateKey($commandData['targetKey']);
            $app->data->rename($commandData['sourceKey'], $commandData['targetKey']);
        } elseif ($command === 'makePublic') {
            $validateKey($commandData['key']);
            $app->data->makePublic($commandData['key']);
        } elseif ($command === 'makePrivate') {
            $validateKey($commandData['key']);
            $app->data->makePrivate($commandData['key']);
        }
        $result[] = $commandResult;
    }
    return $result;
};
