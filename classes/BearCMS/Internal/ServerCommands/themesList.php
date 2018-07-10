<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function() {
    $themes = BearCMS\Internal\Themes::getList();
    $result = [];
    foreach ($themes as $id) {
        $themeManifest = BearCMS\Internal\Themes::getManifest($id);
        $themeData = $themeManifest;
        $themeData['id'] = $id;
        $themeData['hasOptions'] = sizeof(BearCMS\Internal\Themes::getOptions($id)) > 0;
        $result[] = $themeData;
    }
    return $result;
};
