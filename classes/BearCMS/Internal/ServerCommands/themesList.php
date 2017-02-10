<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function() {
    $themes = BearCMS\Internal\Data\Themes::getList();
    $result = [];
    foreach ($themes as $theme) {
        if (isset($theme['manifestFilename'])) {
            $manifestData = BearCMS\Internal\Data\Themes::getManifestData($theme['manifestFilename'], $theme['dir']);
            $manifestData['id'] = $theme['id'];
            if (isset($manifestData['options'])) {
                $manifestData['hasOptions'] = !empty($manifestData['options']);
                unset($manifestData['options']);
            } else {
                $manifestData['hasOptions'] = false;
            }
            $result[] = $manifestData;
        } elseif ($theme['id'] === 'none') {
            $result[] = [
                'id' => 'none',
                'hasOptions' => false
            ];
        }
    }
    return $result;
};
