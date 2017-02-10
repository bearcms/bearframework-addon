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
    if (!isset($data['id'])) {
        throw new Exception('');
    }

    if ($data['id'] === 'none') {
        return ['id' => 'none'];
    }

    $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
    $themes = BearCMS\Internal\Data\Themes::getList();
    foreach ($themes as $theme) {
        if ($theme->id === $data['id']) {
            if (isset($theme->manifestFilename)) {
                $manifestData = BearCMS\Internal\Data\Themes::getManifestData($theme->manifestFilename, $theme->dir);
                $manifestData['id'] = $theme->id;
                if (isset($manifestData['options'])) {
                    $manifestData['hasOptions'] = !empty($manifestData['options']);
                } else {
                    $manifestData['hasOptions'] = false;
                }
                if ($includeOptions) {
                    $manifestData['options'] = [
                        'definition' => isset($manifestData['options']) ? $manifestData['options'] : []
                    ];


                    $result = $app->data->getValue('bearcms/themes/theme/' . md5($theme->id) . '.json');
                    if ($result !== null) {
                        $temp = json_decode($result, true);
                        $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                    } else {
                        $optionsValues = [];
                    }
                    $manifestData['options']['activeValues'] = $optionsValues;

                    $result = $app->data->getValue('.temp/bearcms/userthemeoptions/' . md5($app->bearCMS->currentUser->getID()) . '/' . md5($data['id']) . '.json');
                    if ($result !== null) {
                        $temp = json_decode($result, true);
                        $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                    } else {
                        $optionsValues = null;
                    }
                    $manifestData['options']['currentUserValues'] = $optionsValues;
                } else {
                    if (isset($manifestData['options'])) {
                        unset($manifestData['options']);
                    }
                }
                return $manifestData;
            }
        }
    }
    return null;
};
