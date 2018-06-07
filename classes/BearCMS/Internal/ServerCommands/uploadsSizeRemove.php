<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function($data) {
    BearCMS\Internal\Data\UploadsSize::remove($data['key']);
};
