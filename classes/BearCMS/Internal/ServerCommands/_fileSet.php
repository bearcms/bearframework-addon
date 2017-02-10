<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    if (!isset($data['filename'])) {
        throw new Exception('');
    }
    if (!isset($data['data'])) {
        throw new Exception('');
    }
    $fileData = $data['data'];
    $currentFileData = self::file(['filename' => $data['filename']]);
    if (isset($fileData['name']) && $currentFileData['name'] !== $fileData['name']) {
        $updateKey = function($key) {
            $originalKey = $key;
            $key = preg_replace('/[^a-z0-9\.\-\_]+/u', '-', strtolower($key));
            while (strpos($key, '--') !== false) {
                $key = str_replace('--', '-', $key);
            }
            $key = trim($key, '-');
            $info = pathinfo($key);
            $info['filename'] = trim($info['filename'], '-');
            if (strlen($info['filename']) === 0) {
                $info['filename'] = md5($originalKey);
            }
            if (strlen($key) > 80) {
                $info['filename'] = substr($info['filename'], 0, 80);
            }
            $key = $info['filename'] . (isset($info['extension']) ? '.' . $info['extension'] : '');
            return $key;
        };
        $sourceKey = 'bearcms/files/custom/' . $updateKey($data['filename']);
        $targetKey = 'bearcms/files/custom/' . $updateKey($fileData['name']);
        if ($sourceKey !== $targetKey && is_file($app->data->getFilename($sourceKey))) {
            if (is_file($app->data->getFilename($targetKey))) {
                $info = pathinfo($targetKey);
                if (isset($info['extension'])) {
                    $targetKeyPrefix = substr($targetKey, 0, strlen($targetKey) - strlen($info['extension']) - 1);
                } else {
                    $targetKeyPrefix = $targetKey;
                }
                $done = false;
                for ($i = 1; $i < 9999999; $i++) {
                    $tempTargetKey = $targetKeyPrefix . '_' . $i . (isset($info['extension']) ? '.' . $info['extension'] : '');
                    if (!is_file($app->data->getFilename($tempTargetKey))) {
                        $targetKey = $tempTargetKey;
                        $done = true;
                        break;
                    }
                }
                if (!$done) {
                    throw new Exception('Cannot find available filename for ' . $targetKey);
                }
            }
            $app->data->rename($sourceKey, $targetKey);
            $data['filename'] = str_replace('bearcms/files/custom/', '', $targetKey);
        }
    }
    $key = 'bearcms/files/custom/' . $data['filename'];
    if (isset($fileData['name'])) {
        $app->data->setMetadata($key, 'name', (string) $fileData['name']);
    }
    if (isset($fileData['published'])) {
        $app->data->setMetadata($key, 'published', (string) $fileData['published']);
    }
};
