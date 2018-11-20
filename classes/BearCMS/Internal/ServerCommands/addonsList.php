<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function() {
    return \BearCMS\Internal\Data\Addons::getList()->toArray();
};
