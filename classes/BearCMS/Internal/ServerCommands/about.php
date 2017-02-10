<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

return function() {
    $result = [];
    $result['siteID'] = BearCMS\Internal\Options::$siteID;
    $result['phpVersion'] = phpversion();
    $result['frameworkVersion'] = App::VERSION;
    $result['addonVersion'] = BearCMS::VERSION;
    return $result;
};
