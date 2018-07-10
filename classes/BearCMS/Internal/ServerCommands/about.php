<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function() {
    $result = [];
    if (strlen(BearCMS\Internal\Options::$appSecretKey) > 0) {
        $temp = explode('-', BearCMS\Internal\Options::$appSecretKey);
        $result['appID'] = $temp[0];
    }
    $result['phpVersion'] = phpversion();
    return $result;
};
