<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal\Data;

use BearFramework\App;

class Templates
{

    static function getActiveTemplateID()
    {
        $app = App::$instance;
        $data = $app->data->get(
                [
                    'key' => 'bearcms/templates/active.json',
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

    static function getTemplatesList()
    {
        $app = App::$instance;
        $addonsList = $app->addons->getList();
        $result = [];
        foreach ($addonsList as $addonData) {
            $addonID = $addonData['id'];
            $options = \BearFramework\Addons::getOptions($addonID);
            if (isset($options['bearCMS']) && is_array($options['bearCMS']) && isset($options['bearCMS']['templates']) && is_array($options['bearCMS']['templates'])) {
                $addonDir = trim(\BearFramework\Addons::getDir($addonID), '/') . '/';
                foreach ($options['bearCMS']['templates'] as $templateData) {
                    if (isset($templateData['id'], $templateData['manifest'])) {
                        $manifestFilename = $addonDir . $templateData['manifest'];
                        if (is_file($manifestFilename)) {
                            $templateID = (string) $templateData['id'];
                            $result[$templateID] = ['id' => $templateID, 'dir' => $addonDir, 'manifestFilename' => $manifestFilename];
                        }
                    }
                }
            }
        }
        $result = array_values($result);
        $context = $app->getContext(__DIR__);
        $addonDir = rtrim($context->dir, '/') . '/';
        array_unshift($result, [
            'id' => 'bearcms/default1',
            'dir' => $addonDir,
            'manifestFilename' => $addonDir . 'default-template-1.manifest.json'
        ]);
        array_unshift($result, [
            'id' => 'none'
        ]);
        return $result;
    }

    static function getManifestData($manifestFilename, $dir)
    {
        $data = json_decode(file_get_contents($manifestFilename), true);
        if (isset($data['media']) && is_array($data['media'])) {
            foreach ($data['media'] as $i => $media) {
                if (isset($media['filename'])) {
                    $data['media'][$i]['filename'] = $dir . $media['filename'];
                }
            }
        }
        return $data;
    }

}
