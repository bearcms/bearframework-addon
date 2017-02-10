<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

BearFramework\Addons::register('bearcms/bearframework-addon', __DIR__, [
    'require' => [
        //'bearframework/maintenance-addon',
        'ivopetkov/html-server-components-bearframework-addon',
        'ivopetkov/image-gallery-bearframework-addon',
        'ivopetkov/navigation-menu-bearframework-addon',
        'ivopetkov/form-bearframework-addon',
        'ivopetkov/users-bearframework-addon',
        'ivopetkov/server-requests-bearframework-addon'
    ]
]);
