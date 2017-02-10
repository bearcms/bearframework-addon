<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $app = App::get();
    $result = $app->data->getList()
            ->filterBy('key', 'bearcms/addons/addon/', 'startWith');
    $temp = [];
    foreach ($result as $item) {
        $addonData = json_decode($item->value, true);
        if (isset($addonData['id'])) {
            $addonManifestData = BearCMS\Internal\Data\Addons::getManifestData($addonData['id']);
            if (is_array($addonManifestData)) {
                $addonData['name'] = $addonManifestData['name'];
                $addonData['hasOptions'] = isset($addonManifestData['options']) && !empty($addonManifestData['options']);
            } else {
                $addonData['name'] = $addonData['id'];
                $addonData['hasOptions'] = false;
            }
            if (isset($addonData['options'])) {
                unset($addonData['options']);
            }
            $temp[] = $addonData;
        }
    }
    return $temp;
};
