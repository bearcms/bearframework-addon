<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function($data) {
    $app = App::get();
    if (!isset($data['id'])) {
        throw new Exception('');
    }
    $themeID = $data['id'];

    $includeOptions = isset($data['includeOptions']) && !empty($data['includeOptions']);
    $themes = BearCMS\Internal\Themes::getList();
    foreach ($themes as $id) {
        if ($id === $themeID) {
            $options = BearCMS\Internal\Themes::getOptions($id);
            $themeManifest = BearCMS\Internal\Themes::getManifest($id);
            $themeData = $themeManifest;
            $themeData['id'] = $id;
            $themeData['hasOptions'] = sizeof($options) > 0;
            $themeData['hasStyles'] = sizeof(BearCMS\Internal\Themes::getStyles($id)) > 0;
            if ($includeOptions) {
                $themeData['options'] = [
                    'definition' => $options
                ];
                $result = \BearCMS\Internal\Data::getValue('bearcms/themes/theme/' . md5($id) . '.json');
                if ($result !== null) {
                    $temp = json_decode($result, true);
                    $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                } else {
                    $optionsValues = [];
                }
                $themeData['options']['activeValues'] = $optionsValues;

                $result = \BearCMS\Internal\Data::getValue('.temp/bearcms/userthemeoptions/' . md5($app->bearCMS->currentUser->getID()) . '/' . md5($id) . '.json');
                if ($result !== null) {
                    $temp = json_decode($result, true);
                    $optionsValues = isset($temp['options']) ? $temp['options'] : [];
                } else {
                    $optionsValues = null;
                }
                $themeData['options']['currentUserValues'] = $optionsValues;
            }
            return $themeData;
        }
    }
    return null;
};
