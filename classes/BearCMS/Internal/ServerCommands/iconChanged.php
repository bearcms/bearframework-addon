<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearCMS\Internal\Cookies;

return function() {
    Cookies::setList(Cookies::TYPE_CLIENT, [['name' => 'fc', 'value' => uniqid(), 'expire' => time() + 86400 + 1000]]);
};
