<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

return function($data) {
    Internal\Data\UploadsSize::remove($data['key']);
};
