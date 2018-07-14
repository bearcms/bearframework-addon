<?php

/*
 * BearCMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

BearFramework\Addons::register('bearcms/bearframework-addon', __DIR__, [
    'require' => [
        'bearframework/emails-addon',
        'bearframework/localization-addon',
        'bearframework/tasks-addon',
        'ivopetkov/html-server-components-bearframework-addon',
        'ivopetkov/image-gallery-bearframework-addon',
        'ivopetkov/navigation-menu-bearframework-addon',
        //'ivopetkov/users-bearframework-addon', // Added in index.php
        'ivopetkov/server-requests-bearframework-addon',
        'ivopetkov/form-bearframework-addon',
        'ivopetkov/data-bundle-bearframework-addon',
        'ivopetkov/encryption-bearframework-addon',
        'ivopetkov/notifications-bearframework-addon'
    ]
]);
