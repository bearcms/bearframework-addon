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

    if (BearFramework\Addons::exists($data['id'])) {
        $addonData = [];
        $addonData['id'] = $data['id'];

        $result = $app->data->getValue('bearcms/addons/addon/' . md5($data['id']) . '.json');
        if ($result !== null) {
            $temp = json_decode($result, true);
            $addonData['enabled'] = isset($temp['enabled']) ? (int) $temp['enabled'] > 0 : false;
            $optionsValues = isset($temp['options']) ? $temp['options'] : [];
        } else {
            $addonData['enabled'] = false;
            $optionsValues = [];
        }

        $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
        $addonManifestData = BearCMS\Internal\Data\Addons::getManifestData($data['id']);
        if (is_array($addonManifestData)) {
            $addonData['hasOptions'] = isset($addonManifestData['options']) && !empty($addonManifestData['options']);
            if ($includeOptions) {
                $addonData['options'] = [];
                $addonData['options']['definition'] = isset($addonManifestData['options']) ? $addonManifestData['options'] : [];
                $addonData['options']['values'] = $optionsValues;
                $addonData['options']['valid'] = BearCMS\Internal\Data\Addons::validateOptions($addonData['options']['definition'], $addonData['options']['values']);
            }
            unset($addonManifestData['options']);
            $addonData = array_merge($addonData, $addonManifestData);
        } else {
            $addonData['hasOptions'] = false;
            if ($includeOptions) {
                $addonData['options'] = [];
                $addonData['options']['definition'] = [];
                $addonData['options']['values'] = [];
                $addonData['options']['valid'] = true;
            }
        }
        return $addonData;
    }
    return null;
};
