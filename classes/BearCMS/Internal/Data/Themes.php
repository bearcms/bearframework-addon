<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

final class Themes
{

    static function getActiveThemeID()
    {
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/themes/active.json',
                    'result' => ['key', 'body']
                ]
        );
        if (isset($data['body'])) {
            $data = json_decode($data['body'], true);
            if (isset($data['id'])) {
                return $data['id'];
            }
        }
        return 'none';
    }

    static function getList()
    {
        $app = App::$instance;
        $addonsList = $app->addons->getList();
        $result = [];
        foreach ($addonsList as $addonData) {
            $addonID = $addonData['id'];
            $addonData = \BearFramework\Addons::get($addonID);
            $addonOptions = $addonData['options'];
            if (isset($addonOptions['bearCMS']) && is_array($addonOptions['bearCMS']) && isset($addonOptions['bearCMS']['themes']) && is_array($addonOptions['bearCMS']['themes'])) {
                foreach ($addonOptions['bearCMS']['themes'] as $themeData) {
                    if (is_array($themeData) && isset($themeData['id'], $themeData['manifest']) && is_string($themeData['id']) && is_string($themeData['manifest'])) {
                        $manifestFilename = $addonOptions['dir'] . '/' . $themeData['manifest'];
                        if (is_file($manifestFilename)) {
                            $themeID = $themeData['id'];
                            $result[$themeID] = ['id' => $themeID, 'dir' => $addonOptions['dir'], 'manifestFilename' => $manifestFilename];
                        }
                    }
                }
            }
        }
        $result = array_values($result);
        $context = $app->getContext(__DIR__);
        array_unshift($result, [
            'id' => 'bearcms/default1',
            'dir' => $context->dir,
            'manifestFilename' => $context->dir . '/themes/default1/manifest.json'
        ]);
        array_unshift($result, [
            'id' => 'none'
        ]);
        return $result;
    }

    static function getManifestData($manifestFilename, $contextDir)
    {
        $data = json_decode(file_get_contents($manifestFilename), true);
        if (isset($data['media']) && is_array($data['media'])) {
            foreach ($data['media'] as $i => $media) {
                if (is_array($media) && isset($media['filename'])) {
                    $data['media'][$i]['filename'] = $contextDir . '/' . $media['filename'];
                }
            }
        }
        return $data;
    }

}
