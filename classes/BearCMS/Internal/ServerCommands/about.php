<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Config;

return function() {
    $result = [];
    if (strlen(Config::$appSecretKey) > 0) {
        $temp = explode('-', Config::$appSecretKey);
        $result['appID'] = $temp[0];
    }
    $result['phpVersion'] = phpversion();
    return $result;
};
